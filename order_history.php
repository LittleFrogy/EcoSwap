<?php
session_start();
include 'includes/db_connect.php';
include 'includes/header.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch the orders from the database
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE UserID = ? ORDER BY OrderDate DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if the user has any orders
if (empty($orders)) {
    echo "<p>You have no orders yet.</p>";
    exit();
}

?>

<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/order_history.css">

<main class="container">
    <h1>Your Order History</h1>

    <?php foreach ($orders as $order): ?>
        <div class="order-card">
            <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['OrderID']); ?></p>
            <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['OrderDate']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($order['Status']); ?></p>
            <p><strong>Total Amount:</strong> $<?php echo number_format($order['TotalAmount'], 2); ?></p>
            
            <!-- Button to view details of each order (you can create a new page to view details) -->
            <a href="view_order.php?order_id=<?php echo $order['OrderID']; ?>" class="btn">View Order Details</a>
        </div>
    <?php endforeach; ?>

</main>

<?php include 'includes/footer.php'; ?>
