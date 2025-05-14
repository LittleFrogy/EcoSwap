<?php
session_start();
include 'includes/db_connect.php';
include 'includes/header.php';

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    echo "Invalid order ID.";
    exit();
}

// Fetch order details from the database
$stmt = $pdo->prepare("SELECT * FROM orders WHERE OrderID = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "Order not found.";
    exit();
}

// Fetch the products in the order
$stmt = $pdo->prepare("SELECT p.ProductName, od.Quantity, od.Price FROM order_details od JOIN products p ON od.ProductID = p.ProductID WHERE od.OrderID = ?");
$stmt->execute([$order_id]);
$order_details = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<main class="container">
    <h1>Order Details</h1>
    <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['OrderID']); ?></p>
    <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['OrderDate']); ?></p>
    <p><strong>Status:</strong> <?php echo htmlspecialchars($order['Status']); ?></p>
    <p><strong>Total Amount:</strong> $<?php echo number_format($order['TotalAmount'], 2); ?></p>

    <h2>Order Items</h2>
    <ul>
        <?php foreach ($order_details as $item): ?>
            <li>
                <p><strong><?php echo htmlspecialchars($item['ProductName']); ?></strong></p>
                <p>Quantity: <?php echo $item['Quantity']; ?></p>
                <p>Price: $<?php echo number_format($item['Price'], 2); ?></p>
            </li>
        <?php endforeach; ?>
    </ul>
</main>

<?php include 'includes/footer.php'; ?>
