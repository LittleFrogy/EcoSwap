<?php
session_start();
include 'includes/header.php';
include 'includes/db_connect.php';

// Ensure user is logged in and has the role of 'company'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'company') {
    header("Location: login.php");
    exit;
}
?>

<link rel="stylesheet" href="css/style.css">

<main class="sales-history-container">
    <h1>Sales History</h1>

    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Total Amount</th>
                <th>Order Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch order data for the logged-in company using user_id
            $stmt = $pdo->prepare("
                SELECT o.OrderID, o.Name, o.TotalAmount, o.OrderDate, o.Status
                FROM orders o
                WHERE o.UserID = ?
                ORDER BY o.OrderDate DESC
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($orders) {
                foreach ($orders as $order) {
                    echo "<tr>
                            <td>" . htmlspecialchars($order['OrderID']) . "</td>
                            <td>" . htmlspecialchars($order['Name']) . "</td>
                            <td>$" . number_format($order['TotalAmount'], 2) . "</td>
                            <td>" . htmlspecialchars($order['OrderDate']) . "</td>
                            <td>" . htmlspecialchars($order['Status']) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No sales records found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</main>

<?php include 'includes/footer.php'; ?>
