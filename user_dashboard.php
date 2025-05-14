<?php
session_start();
include 'includes/header.php';
include 'includes/db_connect.php'; // Include database connection

// Ensure user is logged in and has the role of 'user'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

// Check if the user clicked the "edit profile" button
if (isset($_GET['edit_profile']) && $_GET['edit_profile'] == 'true') {
    header("Location: edit_profile.php");
    exit;
}
?>

<link rel="stylesheet" href="css/user_dashboard.css">
<link rel="stylesheet" href="css/style.css">

<main class="dashboard-container">
    <!-- Dashboard Header -->
    <section class="dashboard-header">
        <h1>Welcome to Your Dashboard, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p>Manage your profile, products, and history.</p>
    </section>

    <!-- Profile Section -->
    <section class="dashboard-profile">
        <img src="images/<?php echo htmlspecialchars($_SESSION['profile_image'] ?? 'default.jpg'); ?>" alt="Profile Image">
        <div class="dashboard-profile-info">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($_SESSION['phone'] ?? 'Not provided'); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($_SESSION['address'] ?? 'Not provided'); ?></p>
        </div>
        <a href="edit_profile.php" class="btn">Edit Profile</a>
    </section>

    <!-- Dashboard Actions -->
    <section class="dashboard-actions">
        <a href="add_product.php" class="btn">Add Product</a>
    </section>

    <!-- Your Products Section -->
    <section class="user-products">
        <h2>Your Products</h2>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->prepare("
                    SELECT p.*, c.CategoryName 
                    FROM products p 
                    LEFT JOIN categories c ON p.CategoryID = c.CategoryID 
                    WHERE p.UserID = ? 
                ");
                $stmt->execute([$_SESSION['user_id']]);
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($products) {
                    foreach ($products as $product) {
                        $category = htmlspecialchars($product['CategoryName'] ?? 'Uncategorized');
                        echo "<tr>
                                <td>" . htmlspecialchars($product['ProductName']) . "</td>
                                <td>$" . number_format($product['Price'], 2) . "</td>
                                <td>" . $category . "</td>
                                <td>
                                    <a href='edit_product.php?id=" . $product['ProductID'] . "'>Edit</a> |
                                    <a href='delete_product.php?id=" . $product['ProductID'] . "' onclick=\"return confirm('Are you sure you want to delete this product?');\">Delete</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>You have not added any products yet.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </section>

    <section class="order-history">
        <h2>Your Order History</h2>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Payment Status</th>
                    <th>Total Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch user's orders with payment status
                $stmt = $pdo->prepare("
                    SELECT o.OrderID, o.OrderDate, o.TotalAmount, p.PaymentStatus
                    FROM orders o
                    LEFT JOIN payments p ON o.PaymentID = p.PaymentID
                    WHERE o.UserID = ?
                    ORDER BY o.OrderDate DESC
                ");
                $stmt->execute([$_SESSION['user_id']]);
                $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($orders) {
                    foreach ($orders as $order) {
                        $payment_status = htmlspecialchars($order['PaymentStatus'] ?? 'Unknown');
                        echo "<tr>
                                <td>" . htmlspecialchars($order['OrderID']) . "</td>
                                <td>" . htmlspecialchars($order['OrderDate']) . "</td>
                                <td>" . $payment_status . "</td>
                                <td>$" . number_format($order['TotalAmount'], 2) . "</td>
                                <td>
                                    <a href='submit_review.php?order_id=" . htmlspecialchars($order['OrderID']) . "' class='btn'>Submit Review</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>You have no orders yet.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </section>

    <!-- Sales History Section -->
    <section class="sales-history">
        <h2>Your Sales History</h2>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Product Name</th>
                    <th>Buyer</th>
                    <th>Order Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch sales history
                $stmt = $pdo->prepare("
                    SELECT o.*, p.ProductName, u.Name AS BuyerName
                    FROM orders o
                    JOIN order_details od ON o.OrderID = od.OrderID
                    JOIN products p ON od.ProductID = p.ProductID
                    JOIN users u ON o.UserID = u.UserID
                    WHERE p.UserID = ? 
                    ORDER BY o.OrderDate DESC
                ");
                $stmt->execute([$_SESSION['user_id']]);
                $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($sales) {
                    foreach ($sales as $sale) {
                        echo "<tr>
                                <td>" . htmlspecialchars($sale['OrderID']) . "</td>
                                <td>" . htmlspecialchars($sale['ProductName']) . "</td>
                                <td>" . htmlspecialchars($sale['BuyerName']) . "</td>
                                <td>" . htmlspecialchars($sale['OrderDate']) . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No sales history available.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </section>
</main>

<?php
include 'includes/footer.php';
?>