<?php
session_start();
include 'includes/db_connect.php';

// Ensure the user is logged in, has the correct role, and the request is POST
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

// Retrieve and validate input
$order_id = filter_input(INPUT_POST, 'order_id', FILTER_SANITIZE_NUMBER_INT);
$delivery_status = filter_input(INPUT_POST, 'delivery_status', FILTER_SANITIZE_STRING);

// Validate delivery status options
$valid_statuses = ['Delivered', 'Not Delivered'];
if (!in_array($delivery_status, $valid_statuses)) {
    $_SESSION['error_message'] = "Invalid delivery status.";
    header("Location: user_dashboard.php");
    exit;
}

// Check if the delivery status has already been set
$stmt = $pdo->prepare("SELECT DeliveryStatus FROM orders WHERE OrderID = ? AND SellerID = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    $_SESSION['error_message'] = "Order not found.";
    header("Location: user_dashboard.php");
    exit;
}

if (!is_null($order['DeliveryStatus']) && $order['DeliveryStatus'] !== 'Pending') {
    $_SESSION['error_message'] = "Delivery status has already been updated.";
    header("Location: user_dashboard.php");
    exit;
}

// Update delivery status
$stmt = $pdo->prepare("UPDATE orders SET DeliveryStatus = ? WHERE OrderID = ? AND SellerID = ?");
$stmt->execute([$delivery_status, $order_id, $_SESSION['user_id']]);

if ($stmt->rowCount() > 0) {
    $_SESSION['success_message'] = "Delivery status updated to '$delivery_status'.";
} else {
    $_SESSION['error_message'] = "Failed to update delivery status.";
}

// Redirect back to the dashboard
header("Location: user_dashboard.php");
exit;
?>
