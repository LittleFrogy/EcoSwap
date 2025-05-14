<?php
session_start();
include 'includes/db_connect.php'; // Include database connection

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get product ID from the query parameter
$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    header("Location: user_dashboard.php?error=InvalidProduct"); // Redirect to user dashboard if invalid
    exit;
}

// Fetch product from the database
$stmt = $pdo->prepare("SELECT * FROM products WHERE ProductID = :product_id AND UserID = :user_id");
$stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: user_dashboard.php?error=ProductNotFound"); // Redirect to user dashboard if product not found
    exit;
}

// Handle form submission
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = trim($_POST['product_name']);
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $product_condition = $_POST['product_condition'];
    $description = trim($_POST['description']);

    // Validate inputs
    if (empty($product_name) || empty($price) || empty($category_id) || empty($product_condition)) {
        $error = "All fields are required.";
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = "Price must be a positive number.";
    } elseif (!is_numeric($category_id)) {
        $error = "Invalid category.";
    } else {
        // Update the product in the database
        try {
            $stmt = $pdo->prepare("UPDATE products 
                SET ProductName = :product_name, Price = :price, ProductDescription = :description, 
                    ProductCondition = :product_condition, CategoryID = :category_id 
                WHERE ProductID = :product_id");
            $stmt->bindParam(':product_name', $product_name);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':product_condition', $product_condition);
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->execute();

            // Redirect to the appropriate dashboard based on user role
            if ($_SESSION['role'] === 'admin') {
                header("Location: admin_dashboard.php?success=ProductUpdated");
            } elseif ($_SESSION['role'] === 'company') {
                header("Location: company_dashboard.php?success=ProductUpdated");
            } else {
                header("Location: user_dashboard.php?success=ProductUpdated");
            }
            exit;
        } catch (PDOException $e) {
            $error = "An error occurred while updating the product.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="css/add_product.css">
</head>
<body>

<div class="container">
    <h2>Edit Product</h2>
    <?php if (!empty($error)) { echo "<p class='error'>$error</p>"; } ?>

    <form action="edit_product.php?id=<?php echo htmlspecialchars($product['ProductID']); ?>" method="POST">
        <label for="product_name">Product Name</label>
        <input type="text" name="product_name" id="product_name" 
               value="<?php echo htmlspecialchars($product['ProductName']); ?>" required>

        <label for="price">Price</label>
        <input type="number" name="price" id="price" step="0.01" 
               value="<?php echo htmlspecialchars($product['Price']); ?>" required>

        <label for="category_id">Category</label>
        <select name="category_id" id="category_id" required>
            <?php
            // Fetch categories
            $stmt = $pdo->query("SELECT * FROM categories");
            while ($category = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $selected = $category['CategoryID'] == $product['CategoryID'] ? 'selected' : '';
                echo "<option value='" . htmlspecialchars($category['CategoryID']) . "' $selected>" . 
                     htmlspecialchars($category['CategoryName']) . "</option>";
            }
            ?>
        </select>

        <label for="product_condition">Condition</label>
        <select name="product_condition" id="product_condition" required>
            <option value="New" <?php echo $product['ProductCondition'] == 'New' ? 'selected' : ''; ?>>New</option>
            <option value="Used - Like New" <?php echo $product['ProductCondition'] == 'Used - Like New' ? 'selected' : ''; ?>>Used - Like New</option>
            <option value="Refurbished" <?php echo $product['ProductCondition'] == 'Refurbished' ? 'selected' : ''; ?>>Refurbished</option>
        </select>

        <label for="description">Product Description</label>
        <textarea name="description" id="description" rows="4"><?php echo htmlspecialchars($product['ProductDescription']); ?></textarea>

        <button type="submit">Update Product</button>
    </form>
</div>

</body>
</html>
