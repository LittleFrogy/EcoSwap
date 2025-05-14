<?php
session_start();
include 'includes/db_connect.php';
include 'includes/header.php';

$order_id = $_GET['order_id'] ?? null;

if (!$order_id || !is_numeric($order_id)) {
    header("Location: order_success.php"); // Redirect back if invalid order ID
    exit();
}

// Fetch order details for verification
$stmt = $pdo->prepare("SELECT * FROM orders WHERE OrderID = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header("Location: order_success.php"); // Redirect back if order not found
    exit();
}

$total_amount = $order['TotalAmount'] ?? 0; // Replace with your column name or calculation
?>
<link rel="stylesheet" href="css/payment.css">
<main class="container">
    <h1>Make Payment</h1>
    <form action="payment_process.php" method="POST">
        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>">
        
        <label for="amount">Payment Amount:</label>
        <input type="number" name="amount" id="amount" value="<?php echo htmlspecialchars($total_amount); ?>" readonly>
        
        <label for="payment_method">Select Payment Method:</label>
        <select name="payment_method" id="payment_method" required>
            <option value="">-- Select Payment Method --</option>
            <option value="Credit Card">Credit Card</option>
            <option value="Debit Card">Debit Card</option>
            <option value="PayPal">PayPal</option>
            <option value="Bank Transfer">Bank Transfer</option>
            <option value="Cash on Delivery">Cash on Delivery</option>
        </select>
        
        <button type="submit" class="btn">Confirm Payment</button>
    </form>
</main>

<?php include 'includes/footer.php'; ?>
