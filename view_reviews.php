<?php
include 'includes/db_connect.php';

$product_id = $_GET['product_id'];
$stmt = $pdo->prepare("SELECT * FROM Reviews WHERE ProductID = ?");
$stmt->execute([$product_id]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($reviews as $review) {
    echo "<p><strong>Rating:</strong> " . htmlspecialchars($review['Rating']) . "/5</p>";
    echo "<p><strong>Review:</strong> " . htmlspecialchars($review['ReviewText']) . "</p>";
    echo "<hr>";
}
?>
