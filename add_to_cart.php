<?php
session_start();

// Check if product ID is provided
if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}

// Get product ID and quantity from the URL
$product_id = (int)$_GET['id'];
$quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1; // Default quantity is 1

// Validate quantity
if ($quantity <= 0) {
    $quantity = 1;
}

// Initialize cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add to cart or update quantity if the product already exists
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id] += $quantity; // Update quantity
} else {
    $_SESSION['cart'][$product_id] = $quantity; // Add new product
}

// Redirect to cart page after adding to the cart
header("Location: cart.php");
exit;
?>
