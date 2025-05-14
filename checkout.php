<?php
session_start();
include 'includes/db_connect.php';
include 'includes/header.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

// Check if the cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Fetch products in the cart
$cart_products = array_keys($_SESSION['cart']); // Product IDs
$placeholders = implode(',', array_fill(0, count($cart_products), '?'));

$stmt = $pdo->prepare("SELECT ProductID, ProductName, Price, image FROM products WHERE ProductID IN ($placeholders)");
$stmt->execute($cart_products);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total price
$total_price = 0;
foreach ($products as $product) {
    $total_price += $product['Price'] * $_SESSION['cart'][$product['ProductID']];
}

// Handle form submission for checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $delivery_address = trim($_POST['delivery_address']);
    $payment_method = $_POST['payment_method'];

    // Validate inputs
    if (empty($name) || empty($phone) || empty($email) || empty($delivery_address) || empty($payment_method)) {
        $error_message = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format!";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $phone)) {
        $error_message = "Invalid phone number format!";
    } else {
        try {
            $pdo->beginTransaction();

            // Insert payment
            $stmt = $pdo->prepare("INSERT INTO payments (PaymentMethod, PaymentStatus, PaymentDate) 
                                   VALUES (?, 'Pending', NOW())");
            $stmt->execute([$payment_method]);
            $payment_id = $pdo->lastInsertId();

            // Insert order
            $stmt = $pdo->prepare("INSERT INTO orders (UserID, OrderDate, Status, TotalAmount, DeliveryAddress, PaymentID) 
                                   VALUES (?, CURDATE(), 'Pending', ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $total_price, $delivery_address, $payment_id]);
            $order_id = $pdo->lastInsertId();

            // Insert order details
            foreach ($products as $product) {
                $quantity = $_SESSION['cart'][$product['ProductID']];
                $stmt = $pdo->prepare("INSERT INTO order_details (OrderID, ProductID, Quantity, Price) 
                                       VALUES (?, ?, ?, ?)");
                $stmt->execute([$order_id, $product['ProductID'], $quantity, $product['Price']]);
            }

            $pdo->commit();

            // Clear cart and redirect to order success page
            unset($_SESSION['cart']);
            header("Location: order_success.php?order_id=" . $order_id);
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Order placement error: " . $e->getMessage());
            $error_message = "An error occurred while placing your order. Please try again.";
        }
    }
}
?>

<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/checkout.css">

<main class="container">
    <h1>Checkout</h1>

    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>

    <section class="order-summary">
        <h2>Order Summary</h2>
        <ul>
            <?php foreach ($products as $product): ?>
                <li>
                    <?php 
                        $image_path = 'Uploads/product_images/' . $product['image'];
                        if (!file_exists($image_path)) {
                            $image_path = 'images/default_product.png';
                        }
                    ?>
                    <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($product['ProductName']); ?>">
                    <div>
                        <p><strong><?php echo htmlspecialchars($product['ProductName']); ?></strong></p>
                        <p>Price: $<?php echo number_format($product['Price'], 2); ?></p>
                        <p>Quantity: <?php echo $_SESSION['cart'][$product['ProductID']]; ?></p>
                        <p>Subtotal: $<?php echo number_format($product['Price'] * $_SESSION['cart'][$product['ProductID']], 2); ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <p><strong>Total: $<?php echo number_format($total_price, 2); ?></strong></p>
    </section>

    <section class="billing-info">
        <h2>Billing & Delivery Information</h2>
        <form action="checkout.php" method="POST">
            <label for="name">Full Name</label>
            <input type="text" name="name" id="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>

            <label for="phone">Phone Number</label>
            <input type="text" name="phone" id="phone" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" required>

            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>

            <label for="delivery_address">Delivery Address</label>
            <textarea name="delivery_address" id="delivery_address" required><?php echo isset($_POST['delivery_address']) ? htmlspecialchars($_POST['delivery_address']) : ''; ?></textarea>

            <label for="payment_method">Payment Method</label>
            <select name="payment_method" id="payment_method" required>
                <option value="Credit Card" <?php echo isset($_POST['payment_method']) && $_POST['payment_method'] == 'Credit Card' ? 'selected' : ''; ?>>Credit Card</option>
                <option value="Debit Card" <?php echo isset($_POST['payment_method']) && $_POST['payment_method'] == 'Debit Card' ? 'selected' : ''; ?>>Debit Card</option>
                <option value="PayPal" <?php echo isset($_POST['payment_method']) && $_POST['payment_method'] == 'PayPal' ? 'selected' : ''; ?>>PayPal</option>
                <option value="Bank Transfer" <?php echo isset($_POST['payment_method']) && $_POST['payment_method'] == 'Bank Transfer' ? 'selected' : ''; ?>>Bank Transfer</option>
                <option value="Cash on Delivery" <?php echo isset($_POST['payment_method']) && $_POST['payment_method'] == 'Cash on Delivery' ? 'selected' : ''; ?>>Cash on Delivery</option>
            </select>

            <button type="submit" class="btn">Place Order</button>
        </form>
    </section>
</main>

<?php
include 'includes/footer.php';
?>