<?php
session_start();
include 'includes/db_connect.php';

// Ensure the user is logged in, has the correct role, and the request is POST
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

// Retrieve and validate input
$order_id = filter_input(INPUT_POST, 'order_id', FILTER_SANITIZE_NUMBER_INT);
$receipt_status = filter_input(INPUT_POST, 'receipt_status', FILTER_SANITIZE_STRING);

// Ensure receipt_status is either "Received" or "Not Received"
if (!in_array($receipt_status, ['Received', 'Not Received'])) {
    $_SESSION['error_message'] = "Invalid receipt status.";
    header("Location: user_dashboard.php");
    exit;
}

// Check if the order exists
$stmt = $pdo->prepare("SELECT ReceiptStatus FROM orders WHERE OrderID = ? AND UserID = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    $_SESSION['error_message'] = "Order not found.";
    header("Location: user_dashboard.php");
    exit;
}

// Prevent updating if the receipt status is already set
if ($order['ReceiptStatus'] !== 'Pending') {
    $_SESSION['error_message'] = "Receipt status has already been updated.";
    header("Location: user_dashboard.php");
    exit;
}

// Update the receipt status to either 'Received' or 'Not Received'
$stmt = $pdo->prepare("UPDATE orders SET ReceiptStatus = ? WHERE OrderID = ? AND UserID = ?");
$stmt->execute([$receipt_status, $order_id, $_SESSION['user_id']]);

if ($stmt->rowCount() > 0) {
    $_SESSION['success_message'] = "Receipt status updated to '{$receipt_status}'.";
} else {
    $_SESSION['error_message'] = "Failed to update receipt status.";
}

// Redirect back to the dashboard
header("Location: user_dashboard.php");
exit;
?>
