<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'User not logged in.']);
    exit;
}

// Include the database connection
include 'db_connect.php';

try {
    // Prepare the SQL query to get products from the database
    $stmt = $pdo->prepare("SELECT id, name, description, price, image FROM products");
    $stmt->execute();

    // Fetch all products
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if products exist and return a standard response
    if ($products) {
        echo json_encode(['success' => true, 'data' => $products]);
    } else {
        echo json_encode(['success' => true, 'data' => []]); // Return empty array if no products found
    }
} catch (PDOException $e) {
    // Log the error for debugging (ensure this is logged securely)
    error_log("Database error during product fetch: " . $e->getMessage());

    // Return a generic error message in case of failure
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'An error occurred while fetching the products. Please try again later.']);
}
?>
