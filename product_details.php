<?php
include 'includes/db_connect.php'; // Include database connection
session_start(); // Start session to check logged-in user

// Check if the product ID is provided in the URL
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Fetch the product details from the database, including the user's name and category
    $stmt = $pdo->prepare("
        SELECT products.*, users.Name AS user_name, categories.CategoryName 
        FROM products 
        JOIN users ON products.UserID = users.UserID 
        LEFT JOIN categories ON products.CategoryID = categories.CategoryID 
        WHERE ProductID = ?
    ");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        // Redirect if product is not found
        header('Location: all_products.php');
        exit();
    }
} else {
    // Redirect if no product ID is provided
    header('Location: all_products.php');
    exit();
}

// Update product status to "sold" if it's been purchased
if ($product['status'] === 'sold') {
    // Ensure we update the status if it's bought
    $stmt = $pdo->prepare("UPDATE products SET status = 'sold' WHERE ProductID = ?");
    $stmt->execute([$product_id]);
}
?>

<link rel="stylesheet" href="css/product_details.css">

<main class="container">
    <section class="product-details">
        <h2><?php echo htmlspecialchars($product['ProductName']); ?></h2>
        
        <!-- Display product image -->
        <?php if (!empty($product['ProductImage'])): ?>
            <img src="uploads/<?php echo htmlspecialchars($product['ProductImage']); ?>" alt="Product Image" class="product-image">
        <?php else: ?>
            <img src="images/placeholder.png" alt="No Image Available" class="product-image">
        <?php endif; ?>
        
        <p><?php echo htmlspecialchars($product['ProductDescription']); ?></p>
        <p><strong>Price:</strong> $<?php echo number_format($product['Price'], 2); ?></p>
        <p><strong>Condition:</strong> <?php echo htmlspecialchars($product['ProductCondition']); ?></p>
        <p><strong>Category:</strong> <?php echo htmlspecialchars($product['CategoryName'] ?? 'Unknown'); ?></p>
        <p><strong>Added By:</strong> <?php echo htmlspecialchars($product['user_name']); ?></p>
        <p><strong>Status:</strong> 
            <?php 
                // Display status as sold or available
                echo $product['status'] === 'sold' ? '<span class="sold-status">Sold</span>' : 'Available'; 
            ?>
        </p>

        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $product['UserID']): ?>
            <a href="edit_product.php?id=<?php echo $product['ProductID']; ?>">Edit Product</a> |
            <a href="delete_product.php?id=<?php echo $product['ProductID']; ?>" onclick="return confirm('Are you sure you want to delete this product?');">Delete Product</a>
        <?php else: ?>
            <!-- No Add to Cart form here, it has been removed -->
            <?php if ($product['status'] === 'sold'): ?>
                <!-- If sold, display a message saying it's sold -->
                <p><span class="sold-status">This product is already sold.</span></p>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</main>

<style>
.sold-status {
    color: red;
    font-weight: bold;
    font-size: 1.2em;
}
</style>
