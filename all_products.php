<?php
// Include the header (navbar, etc.)
include 'includes/header.php';
include 'includes/db_connect.php';

// Fetch all products from the database
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="container">
    <h1>Products Available</h1>
    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                <p>Price: $<?php echo number_format($product['price'], 2); ?></p>
                <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image">

                <!-- Add to Cart or Buy Now -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="buy_product.php?id=<?php echo $product['id']; ?>" class="btn">Buy Now</a>
                <?php else: ?>
                    <a href="login.php?redirect=<?php echo urlencode('buy_product.php?id=' . $product['id']); ?>" class="btn">Login to Buy</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
