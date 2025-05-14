<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page with the current URL for post-login redirection
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

include 'includes/db_connect.php';

// Validate the product ID
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
if (!$product_id) {
    // Redirect to products page if no valid product ID is provided
    header("Location: all_products.php");
    exit();
}

// Fetch product details from the database
$stmt = $pdo->prepare("SELECT * FROM products WHERE ProductID = :product_id");
$stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    // Redirect to products page if the product doesn't exist
    header("Location: all_products.php");
    exit();
}

// Check product availability
if ($product['ProductStatus'] === 'Out of Stock' || $product['ProductStatus'] === 'Discontinued') {
    $_SESSION['error_message'] = 'Sorry, this product is currently unavailable.';
    header("Location: all_products.php");
    exit();
}

// Store product information in the session for potential checkout use
$_SESSION['product_to_buy'] = [
    'id' => $product['ProductID'],
    'name' => $product['ProductName'],
    'price' => $product['Price'],
    'description' => $product['ProductDescription'],
    'image' => !empty($product['Image']) ? $product['Image'] : 'default_product.png' // Default image if none is provided
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buy Product</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/buy_product.css">
</head>
<body>
    <div class="product-details-container">
        <h1>You're about to buy: <?php echo htmlspecialchars($product['ProductName']); ?></h1>

        <div class="product-details">
            <?php
            // Construct the image path
            $image_path = 'uploads/product_images/' . htmlspecialchars($_SESSION['product_to_buy']['image']);
            
            // Check if the image exists, otherwise use the default image
            if (!file_exists($image_path)) {
                $image_path = 'images/default_product.png';
            }
            ?>
            <img src="<?php echo $image_path; ?>" alt="Product Image" onerror="this.src='images/default_product.png';">
            <p><strong>Price:</strong> $<?php echo number_format($product['Price'], 2); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($product['ProductDescription']); ?></p>
        </div>

        <!-- Proceed to Checkout Button -->
        <a href="checkout.php?id=<?php echo $product['ProductID']; ?>" class="btn">Proceed to Checkout</a>
    </div>
</body>
</html>
