<?php
session_start();
include 'includes/header.php';
include 'includes/db_connect.php'; // Include database connection

// Ensure user is logged in and has the role of 'company'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'company') {
    header("Location: login.php");
    exit;
}

?>
<link rel="stylesheet" href="css/company_dashboard.css">
<link rel="stylesheet" href="css/style.css">

<main class="dashboard-container">
    <!-- Dashboard Header -->
    <section class="dashboard-header">
        <h1>Welcome to the Company Dashboard, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p>Here you can manage your products and see your sales history.</p>
    </section>

    <!-- Company Actions Section -->
    <section class="company-actions">
        <a href="add_product.php">Add Product</a> |
        <a href="view_sales.php">View Sales History</a>
    </section>

    <!-- Company Products Section -->
    <section class="company-products">
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
                // Fetch company's products
                $stmt = $pdo->prepare("SELECT * FROM products WHERE UserID = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($products) {
                    foreach ($products as $product) {
                        echo "<tr>
                                <td>" . htmlspecialchars($product['ProductName']) . "</td>
                                <td>$" . number_format($product['Price'], 2) . "</td>
                                <td>" . htmlspecialchars($product['CategoryID']) . "</td>
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
</main>

<?php
include 'includes/footer.php';
?>
