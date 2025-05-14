<?php
session_start();
include 'includes/db_connect.php'; // Include database connection

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// CSRF Protection: Generate token if not already present
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate a new CSRF token
}

// Get product ID
$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    header("Location: dashboard.php?error=NoProductID");
    exit;
}

// Fetch the product from the database to ensure it exists and belongs to the logged-in user
$stmt = $pdo->prepare("SELECT * FROM products WHERE ProductID = :product_id AND UserID = :user_id");
$stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: dashboard.php?error=ProductNotFound");
    exit;
}

// Handle the deletion request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';  // Default to empty string if not set

    // Validate CSRF token
    if (empty($csrf_token) || !hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        header("Location: user_dashboard.php?error=InvalidToken");
        exit;
    }

    try {
        // Prepare and execute delete query
        $stmt = $pdo->prepare("DELETE FROM products WHERE ProductID = :product_id AND UserID = :user_id");
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Log deletion for auditing
            error_log("User {$_SESSION['user_id']} deleted product $product_id.");

            // Redirect back to the appropriate dashboard
            if ($_SESSION['role'] === 'admin') {
                header("Location: admin_dashboard.php?success=ProductDeleted");
            } elseif ($_SESSION['role'] === 'company') {
                header("Location: company_dashboard.php?success=ProductDeleted");
            } else {
                header("Location: user_dashboard.php?success=ProductDeleted");
            }
            exit;
        } else {
            header("Location: user_dashboard.php?error=DeletionFailed");
            exit;
        }
    } catch (PDOException $e) {
        // Log and handle any database errors
        error_log("Error deleting product (User: {$_SESSION['user_id']}, Product: $product_id): " . $e->getMessage());
        header("Location: user_dashboard.php?error=ServerError");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Deletion</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/delete_product.css">
    <!-- Add your CSS link -->
    <script>
        // Confirmation dialog
        function confirmDeletion() {
            return confirm("Are you sure you want to delete this product? This action cannot be undone.");
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Confirm Product Deletion</h2>
        <p>Are you sure you want to delete this product? This action cannot be undone.</p>
        <form action="delete_product.php?id=<?= htmlspecialchars($product_id) ?>" method="POST" onsubmit="return confirmDeletion();">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <button type="submit" class="btn-delete">Yes, Delete</button>
            <a href="user_dashboard.php" class="btn-cancel">Cancel</a>
        </form>
    </div>
</body>
</html>
