<?php
session_start();
include 'includes/db_connect.php'; // Include database connection
include 'includes/header.php';
// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if user is not logged in
    exit;
}

// Allow both company and user roles to add products
if (($_SESSION['role'] == 'company' || $_SESSION['role'] == 'user') && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $product_condition = $_POST['product_condition'];
    $description = $_POST['description'];

    // Handle image upload
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['product_image'];
        $image_name = $image['name'];
        $image_tmp_name = $image['tmp_name'];
        $image_size = $image['size'];

        // Validate image
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $image_type = mime_content_type($image_tmp_name);

        if (!in_array($image_type, $allowed_types)) {
            $error = "Invalid image type. Only JPG, PNG, and GIF are allowed.";
        } elseif ($image_size > 5000000) { // 5MB limit
            $error = "Image size is too large. Maximum size is 5MB.";
        } else {
            // Generate a unique filename and move the uploaded file
            $image_new_name = uniqid('product_', true) . '.' . pathinfo($image_name, PATHINFO_EXTENSION);
            $upload_dir = 'uploads/product_images/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true); // Create the folder if it doesn't exist
            }
            $upload_path = $upload_dir . $image_new_name;
            if (!move_uploaded_file($image_tmp_name, $upload_path)) {
                $error = "Error uploading the image.";
            }
        }
    } else {
        $error = "Please upload an image for the product.";
    }

    // Validate the other inputs
    if (empty($product_name) || empty($price) || empty($category_id) || empty($product_condition) || isset($error)) {
        $error = $error ?? "All fields are required.";
    } else {
        // Insert the new product into the database
        $stmt = $pdo->prepare("INSERT INTO products (ProductName, Price, ProductDescription, ProductCondition, CategoryID, UserID, image) 
                               VALUES (:product_name, :price, :description, :product_condition, :category_id, :user_id, :product_image)");
        $stmt->bindParam(':product_name', $product_name);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':product_condition', $product_condition);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':product_image', $image_new_name);
        $stmt->execute();

        // Redirect to the appropriate dashboard after successful addition
        if ($_SESSION['role'] == 'company') {
            header("Location: company_dashboard.php");
        } else {
            header("Location: user_dashboard.php");
        }
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="css/add_product.css">
</head>
<body>

<div class="container">
    <h2>Add Product</h2>
    <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>

    <form action="add_product.php" method="POST" enctype="multipart/form-data">
        <label for="product_name">Product Name</label>
        <input type="text" name="product_name" id="product_name" required>

        <label for="price">Price</label>
        <input type="number" name="price" id="price" required>

        <label for="category_id">Category</label>
        <select name="category_id" id="category_id" required>
            <?php
            // Fetch categories from the database
            $stmt = $pdo->prepare("SELECT * FROM categories");
            $stmt->execute();
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($categories as $category) {
                echo "<option value='" . $category['CategoryID'] . "'>" . htmlspecialchars($category['CategoryName']) . "</option>";
            }
            ?>
        </select>

        <label for="product_condition">Condition</label>
        <select name="product_condition" id="product_condition" required>
            <option value="New">New</option>
            <option value="Used - Like New">Used - Like New</option>
            <option value="Refurbished">Refurbished</option>
        </select>

        <label for="description">Product Description</label>
        <textarea name="description" id="description" rows="4"></textarea>

        <label for="product_image">Product Image:</label>
        <input type="file" id="product_image" name="product_image" accept="image/*" required>

        <button type="submit">Add Product</button>
    </form>
</div>

</body>
</html>