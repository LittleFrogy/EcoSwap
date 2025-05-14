<?php
session_start();
include 'includes/db_connect.php';
include 'includes/header.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Initialize variables for stats
$userCount = $productCount = $companyCount = 0;

try {
    // Fetch dashboard statistics
    $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $companyCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'company'")->fetchColumn();

    // Fetch all users
    $usersStmt = $pdo->query("SELECT UserID, Name, Email, Role FROM users ORDER BY Role ASC");

    // Fetch all products
    $productsStmt = $pdo->query("SELECT ProductID, ProductName, Price, status FROM products ORDER BY ProductName ASC");
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin_dashboard.css">
</head>
<body>
    <div class="container">
        <h1>Welcome, Admin <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

        <!-- Stats Section -->
        <div class="stats">
            <div class="stat-box">
                <h3>Total Users</h3>
                <p><?php echo $userCount; ?></p>
            </div>
            <div class="stat-box">
                <h3>Total Products</h3>
                <p><?php echo $productCount; ?></p>
            </div>
            <div class="stat-box">
                <h3>Total Companies</h3>
                <p><?php echo $companyCount; ?></p>
            </div>
        </div>

        <!-- Manage Users -->
        <div class="management-section">
            <h2>Manage Users</h2>
            <table>
                <thead>
                    <tr>
                        <th>UserID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $usersStmt->fetch(PDO::FETCH_ASSOC)) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['UserID']); ?></td>
                            <td><?php echo htmlspecialchars($user['Name']); ?></td>
                            <td><?php echo htmlspecialchars($user['Email']); ?></td>
                            <td><?php echo htmlspecialchars($user['Role']); ?></td>
                            <td class="actions">
                                <a href="edit_user.php?id=<?php echo $user['UserID']; ?>">Edit</a>
                                <a href="delete_user.php?id=<?php echo $user['UserID']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Manage Products -->
        <div class="management-section">
            <h2>Manage Products</h2>
            <table>
                <thead>
                    <tr>
                        <th>ProductID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($product = $productsStmt->fetch(PDO::FETCH_ASSOC)) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['ProductID']); ?></td>
                            <td><?php echo htmlspecialchars($product['ProductName']); ?></td>
                            <td>$<?php echo number_format($product['Price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($product['status']); ?></td>
                            <td class="actions">
                                <!-- Removed Edit button for products -->
                                <a href="delete_product.php?id=<?php echo $product['ProductID']; ?>" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
