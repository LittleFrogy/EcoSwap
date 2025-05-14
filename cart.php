<?php
session_start();
include 'includes/db_connect.php';
include 'includes/header.php';

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle update/remove functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['remove'])) {
        // Remove product from cart
        $product_id = (int)$_POST['product_id'];
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
    }
}

// Fetch all products in the cart
if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));

    // Fetch product details for the products in the cart
    $stmt = $pdo->prepare("SELECT * FROM products WHERE ProductID IN ($placeholders)");
    $stmt->execute($product_ids);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $products = [];
}
?>

<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/cart.css">

<main class="container">
    <h1>Your Cart</h1>

    <section class="cart">
        <?php if (!empty($products)): ?>
            <ul>
                <?php
                $total_price = 0;
                foreach ($products as $product):
                    $quantity = $_SESSION['cart'][$product['ProductID']];
                    $subtotal = $product['Price'] * $quantity;
                    $total_price += $subtotal;

                    // Check if image exists and set the path
                    $image_path = !empty($product['Image']) ? 'uploads/product_images/' . htmlspecialchars($product['Image']) : 'path/to/default-image.jpg';
                    $image_alt = htmlspecialchars($product['ProductName']);
                ?>
                    <li>
                        <!-- Displaying product image and details -->
                        <img src="<?php echo file_exists($image_path) ? $image_path : 'path/to/default-image.jpg'; ?>" alt="<?php echo $image_alt; ?>">
                        <div>
                            <p><strong><?php echo htmlspecialchars($product['ProductName']); ?></strong></p>
                            <p>Price: $<?php echo number_format($product['Price'], 2); ?></p>
                            <p>Quantity: <?php echo $quantity; ?></p>
                            <p>Subtotal: $<?php echo number_format($subtotal, 2); ?></p>

                            <!-- Remove from Cart Form -->
                            <form action="cart.php" method="POST" style="display:inline;">
                                <input type="hidden" name="product_id" value="<?php echo $product['ProductID']; ?>">
                                <button type="submit" name="remove">Remove</button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>

            <p><strong>Total: $<?php echo number_format($total_price, 2); ?></strong></p>

            <a href="checkout.php" class="btn">Proceed to Checkout</a>

        <?php else: ?>
            <p>Your cart is empty!</p>
        <?php endif; ?>
    </section>
</main>

<?php
include 'includes/footer.php';
?>
