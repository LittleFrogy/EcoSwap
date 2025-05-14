<?php
// Start session and include the header
session_start();
include 'includes/header.php';
include 'includes/db_connect.php'; // Include the database connection

// Fetch the logged-in user ID and role
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;  // Get the logged-in user ID
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : null; // Get the logged-in user's role

// Fetch products based on role
try {
    if ($user_role === 'company') {
        // Companies: See all products, but no "Add to Cart"
        $stmt = $pdo->prepare("SELECT ProductName, ProductDescription, Price, image AS Image, ProductID, status FROM products");
    } else {
        // Users: See all products, but cannot add their own to the cart
        $stmt = $pdo->prepare("SELECT ProductName, ProductDescription, Price, image AS Image, ProductID, UserID, status FROM products");
    }

    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching products: " . $e->getMessage());
    $products = []; // Default to an empty array if there's an error
}
?>

<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/products.css">

<main class="container">
    <h1>All Products</h1>

    <section class="product-list">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="product-item">
                    <img src="Uploads/product_images/<?php echo htmlspecialchars($product['Image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['ProductName']); ?>" 
                         onerror="this.src='images/default_product.png';">
                    <div class="product-details">
                        <h3><?php echo htmlspecialchars($product['ProductName']); ?></h3>
                        <p><?php echo htmlspecialchars($product['ProductDescription']); ?></p>
                        <p><strong>$<?php echo number_format($product['Price'], 2); ?></strong></p>

                        <!-- Display product status -->
                        <p><strong>Status:</strong> 
                            <?php echo $product['status'] === 'sold' ? '<span class="sold-status">Sold</span>' : ($product['status'] === 'available' ? 'Available' : 'Unavailable'); ?>
                        </p>

                        <!-- Fetch and display reviews -->
                        <?php
                            $review_stmt = $pdo->prepare("
                                SELECT r.Rating, r.ReviewText, r.CreatedAt, u.Name AS ReviewerName 
                                FROM reviews r
                                JOIN users u ON r.UserID = u.UserID
                                WHERE r.ProductID = ?
                            ");
                            $review_stmt->execute([$product['ProductID']]);
                            $reviews = $review_stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>

                        <!-- Display reviews if available -->
                        <?php if (!empty($reviews)): ?>
                            <div class="product-reviews">
                                <h4>Reviews:</h4>
                                <?php foreach ($reviews as $review): ?>
                                    <div class="review-item">
                                        <p><strong><?php echo htmlspecialchars($review['ReviewerName']); ?></strong> 
                                           - <?php echo htmlspecialchars($review['Rating']); ?>/5</p>
                                        <p><?php echo htmlspecialchars($review['ReviewText']); ?></p>
                                        <p><small>Reviewed on: <?php echo htmlspecialchars($review['CreatedAt']); ?></small></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p>No reviews yet.</p>
                        <?php endif; ?>

                        <?php if ($user_role !== 'company'): ?>
                            <!-- If the user is NOT a company -->
                            <?php if ($product['UserID'] != $user_id && $product['status'] === 'available'): ?>
                                <!-- Show Add to Cart only if the product is not posted by the user and is available -->
                                <a href="add_to_cart.php?id=<?php echo $product['ProductID']; ?>" class="btn">Add to Cart</a>
                            <?php endif; ?>
                            <a href="product_details.php?id=<?php echo $product['ProductID']; ?>" class="btn">View Details</a>
                        <?php else: ?>
                            <!-- If the user is a company -->
                            <p>View Only</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No products available at the moment. Check back later!</p>
        <?php endif; ?>
    </section>
</main>

<?php
// Include the footer
include 'includes/footer.php';
?>

<style>
.sold-status {
    color: red;
    font-weight: bold;
    font-size: 1.2em;
}

.product-reviews {
    margin-top: 20px;
    padding-top: 10px;
    border-top: 1px solid #ccc;
}

.review-item {
    margin-bottom: 15px;
}

.review-item p {
    margin: 5px 0;
}

.review-item strong {
    font-size: 1.1em;
}
</style>