<?php
session_start();
include 'includes/db_connect.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Get the UserID from the request
$user_id = $_GET['id'] ?? null;

if (!$user_id) {
    header("Location: admin_dashboard.php?error=InvalidUser");
    exit;
}

// Prevent the admin from deleting themselves
if ($user_id == $_SESSION['user_id']) {
    header("Location: admin_dashboard.php?error=CannotDeleteSelf");
    exit;
}

// Check if the user exists in the database
$stmt = $pdo->prepare("SELECT * FROM users WHERE UserID = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: admin_dashboard.php?error=UserNotFound");
    exit;
}

// Step 1: Delete reviews associated with the products the user has posted
$stmt = $pdo->prepare("
    DELETE FROM reviews 
    WHERE ProductID IN (SELECT ProductID FROM products WHERE UserID = :user_id)
");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

// Step 2: Delete products posted by the user
$stmt = $pdo->prepare("DELETE FROM products WHERE UserID = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

// Step 3: Delete the user
$stmt = $pdo->prepare("DELETE FROM users WHERE UserID = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

// Redirect back to the admin dashboard with a success message
header("Location: admin_dashboard.php?success=UserDeleted");
exit;
?>
