<?php
session_start();
include 'includes/db_connect.php';
include 'includes/header.php';

$order_id = $_GET['order_id'] ?? null;

if (!$order_id || !is_numeric($order_id)) {
    header("Location: index.php");
    exit();
}

// Fetch order details
$stmt = $pdo->prepare("SELECT o.*, u.Name AS UserName FROM orders o JOIN users u ON o.UserID = u.UserID WHERE o.OrderID = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header("Location: order_history.php");
    exit();
}

// Fetch products associated with the order
$product_stmt = $pdo->prepare("
    SELECT p.ProductID, p.ProductName, p.Price, od.Quantity 
    FROM order_details od
    INNER JOIN products p ON od.ProductID = p.ProductID
    WHERE od.OrderID = ?
");
$product_stmt->execute([$order_id]);
$order_products = $product_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch payment details
$payment_stmt = $pdo->prepare("SELECT * FROM payments WHERE PaymentID = ?");
$payment_stmt->execute([$order['PaymentID']]);
$payment = $payment_stmt->fetch(PDO::FETCH_ASSOC);

// Calculate total amount
$total_amount = 0;
foreach ($order_products as $product) {
    $total_amount += $product['Price'] * $product['Quantity'];
}

// Fetch delivery status
$delivery_status = $order['Status'] ?? 'Pending';
?>

<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/order_success.css">

<main class="container">
    <h1>Order Confirmation</h1>
    <p>Thank you for your purchase! Your order has been successfully placed.</p>
    
    <section class="order-summary">
        <h2>Order Summary</h2>
        <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['OrderID']); ?></p>
        <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($order['UserName']); ?></p>
        <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['OrderDate']); ?></p>
        <p><strong>Total Amount:</strong> $<?php echo number_format($total_amount, 2); ?></p>
        <p><strong>Delivery Address:</strong> <?php echo htmlspecialchars($order['DeliveryAddress']); ?></p>
        <p><strong>Delivery Status:</strong> <?php echo htmlspecialchars($delivery_status); ?></p>
    </section>
    
    <section class="payment-info">
        <h2>Payment Information</h2>
        <?php if ($payment): ?>
            <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($payment['PaymentMethod']); ?></p>
            <p><strong>Payment Status:</strong> <?php echo htmlspecialchars($payment['PaymentStatus']); ?></p>
            <?php if ($payment['PaymentStatus'] === 'Pending'): ?>
                <a href="payment.php?order_id=<?php echo htmlspecialchars($order_id); ?>" class="btn">Make Payment</a>
            <?php endif; ?>
        <?php else: ?>
            <p class="error">Payment details are not available.</p>
            <a href="payment.php?order_id=<?php echo htmlspecialchars($order_id); ?>" class="btn">Make Payment</a>
        <?php endif; ?>
    </section>

    <section class="product-details">
        <h2>Order Details</h2>
        <ul>
            <?php foreach ($order_products as $product): ?>
                <li>
                    <strong>Product Name:</strong> <?php echo htmlspecialchars($product['ProductName']); ?><br>
                    <strong>Price:</strong> $<?php echo number_format($product['Price'], 2); ?><br>
                    <strong>Quantity:</strong> <?php echo $product['Quantity']; ?><br>
                    <strong>Subtotal:</strong> $<?php echo number_format($product['Price'] * $product['Quantity'], 2); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
