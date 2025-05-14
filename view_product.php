<?php
session_start();
include 'includes/header.php';
include 'includes/db_connect.php'; // Include database connection

// Get the product ID from the URL
$product_id = $_GET['id'] ?? null;

if (!$product_id || !is_numeric($product_id)) {
    header("Location: products.php?error=InvalidProductID");
    exit;
}

// Fetch product details from the database
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE ProductID = :product_id");
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        header("Location: products.php?error=ProductNotFound");
        exit;
    }
} catch (PDOException $e) {
    error_log("Error fetching product details: " . $e->getMessage());
    header("Location: products.php?error=ServerError");
    exit;
}
?>

<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/product_details.css">

<main class="container">
    <h1><?php echo htmlspecialchars($product['ProductName']); ?></h1>

    <div class="product-details">
    <img 
    src="images/<?php echo htmlspecialchars($product['Image'] ?? 'default_product.png'); ?>" 
    alt="<?php echo htmlspecialchars($product['ProductName'] ?? 'Product'); ?>" 
    onerror="this.src='images/default_product.png';">


        <div class="product-info">
            <h3>Price: $<?php echo number_format($product['Price'], 2); ?></h3>
            <p><strong>Condition:</strong> <?php echo htmlspecialchars($product['ProductCondition']); ?></p>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($product['CategoryID']); ?></p>
            <p><strong>Description:</strong></p>
            <p><?php echo nl2br(htmlspecialchars($product['ProductDescription'])); ?></p>
        </div>
    </div>

    <div class="product-actions">
        <a href="buy_product.php?id=<?php echo $product['ProductID']; ?>" class="btn">Buy Now</a>
        <a href="add_to_cart.php?id=<?php echo $product['ProductID']; ?>" class="btn">Add to Cart</a>
        <a href="products.php" class="btn">Back to Products</a>
    </div>
</main>

<?php
include 'includes/footer.php';
?>
