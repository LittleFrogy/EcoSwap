<?php
session_start();
include 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? null;
    $amount = $_POST['amount'] ?? 0;
    $payment_method = $_POST['payment_method'] ?? '';

    // Validate inputs
    if (!$order_id || !is_numeric($order_id) || empty($payment_method)) {
        header("Location: payment.php?order_id=$order_id&error=invalid_input");
        exit();
    }

    try {
        $pdo->beginTransaction();

        // Check if order exists and get PaymentID
        $stmt = $pdo->prepare("SELECT PaymentID FROM orders WHERE OrderID = ?");
        $stmt->execute([$order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            throw new Exception("Order not found.");
        }

        $payment_id = $order['PaymentID'];

        if ($payment_id) {
            // Update existing payment record
            $update_stmt = $pdo->prepare("
                UPDATE payments 
                SET PaymentMethod = ?, PaymentStatus = ?, PaymentDate = CURDATE() 
                WHERE PaymentID = ?
            ");
            $update_stmt->execute([$payment_method, 'Paid', $payment_id]);
        } else {
            // Insert new payment record
            $insert_stmt = $pdo->prepare("
                INSERT INTO payments (PaymentMethod, PaymentStatus, PaymentDate) 
                VALUES (?, ?, CURDATE())
            ");
            $insert_stmt->execute([$payment_method, 'Paid']);
            $payment_id = $pdo->lastInsertId();

            // Update orders with the new PaymentID
            $update_order_stmt = $pdo->prepare("UPDATE orders SET PaymentID = ? WHERE OrderID = ?");
            $update_order_stmt->execute([$payment_id, $order_id]);
        }

        // Update order status to Completed
        $update_order_stmt = $pdo->prepare("UPDATE orders SET Status = ? WHERE OrderID = ?");
        $update_order_stmt->execute(['Completed', $order_id]);

        // Update product status to sold for all products in the order
        $update_product_stmt = $pdo->prepare("
            UPDATE products p
            JOIN order_details od ON p.ProductID = od.ProductID
            SET p.status = 'sold'
            WHERE od.OrderID = ?
        ");
        $update_product_stmt->execute([$order_id]);

        $pdo->commit();

        // Redirect to success page
        header("Location: payment_success.php");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Payment processing error: " . $e->getMessage());
        header("Location: payment.php?order_id=$order_id&error=processing_error");
        exit();
    }
}
?>