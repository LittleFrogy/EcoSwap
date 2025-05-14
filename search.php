<?php
include 'includes/db_connect.php'; // Include database connection

// Get the search query
$query = isset($_GET['query']) ? $_GET['query'] : '';

// Prepare the SQL query to fetch products matching the search query
$stmt = $pdo->prepare("SELECT * FROM products WHERE ProductName LIKE ? OR ProductDescription LIKE ?");
$stmt->execute(['%' . $query . '%', '%' . $query . '%']);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="CSS/search.css">

<main class="container">
    <section class="search-results">
        <h2>Search Results</h2>
        <?php if (empty($products)) : ?>
            <p>No products found matching your search criteria.</p>
        <?php else : ?>
            <ul>
                <?php foreach ($products as $product) : ?>
                    <li>
                        <h3><?php echo htmlspecialchars($product['ProductName']); ?></h3>
                        <p><?php echo htmlspecialchars($product['ProductDescription']); ?></p>
                        <p>Price: $<?php echo number_format($product['Price'], 2); ?></p>
                        <a href="product_details.php?id=<?php echo $product['ProductID']; ?>">View Details</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</main>
