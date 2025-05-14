<?php
session_start();
include 'includes/db_connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$order_id = $_GET['order_id'] ?? null;

// If the order_id is not provided, redirect to order history
if (!$order_id) {
    header("Location: user_dashboard.php");
    exit();
}

// Fetch the order and its products to display the review form
$stmt = $pdo->prepare("
    SELECT od.ProductID, p.ProductName
    FROM order_details od
    JOIN products p ON od.ProductID = p.ProductID
    WHERE od.OrderID = ?
");
$stmt->execute([$order_id]);
$order_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If the order doesn't exist or has no products, redirect
if (!$order_products) {
    header("Location: user_dashboard.php");
    exit();
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $review_text = trim($_POST['review_text']);
    $rating = $_POST['review_rating'];

    // Validate inputs
    if (empty($review_text) || $rating < 1 || $rating > 5) {
        echo "<div class='error'>Please provide a valid review and rating (1-5).</div>";
    } else {
        // Check if the user has already reviewed this product
        $stmt = $pdo->prepare("SELECT * FROM reviews WHERE ProductID = ? AND UserID = ?");
        $stmt->execute([$product_id, $_SESSION['user_id']]);
        $existing_review = $stmt->fetch();

        if ($existing_review) {
            echo "<div class='error'>You have already submitted a review for this product.</div>";
        } else {
            // Insert the review into the database
            $stmt = $pdo->prepare("INSERT INTO reviews (ProductID, UserID, Rating, ReviewText, CreatedAt) 
                                   VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$product_id, $_SESSION['user_id'], $rating, $review_text]);

            // Redirect to a success page or show a success message
            header("Location: order_success.php?order_id=" . $order_id . "&review=success");
            exit();
        }
    }
}
?>

<!-- HTML Form for submitting the review -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Review</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/submit_review.css">
</head>
<body>

<h2>Submit Review for Order #<?php echo htmlspecialchars($order_id); ?></h2>

<form method="POST" action="submit_review.php?order_id=<?php echo htmlspecialchars($order_id); ?>">
    <?php foreach ($order_products as $product): ?>
        <div>
            <h3><?php echo htmlspecialchars($product['ProductName']); ?></h3>
            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['ProductID']); ?>">
            <label for="review_text">Review:</label><br>
            <textarea name="review_text" rows="4" required placeholder="Write your review here..."></textarea><br>

            <label for="review_rating">Rating (1-5):</label><br>
            <select name="review_rating" required>
                <option value="">Select Rating</option>
                <option value="1">1 - Terrible</option>
                <option value="2">2 - Poor</option>
                <option value="3">3 - Average</option>
                <option value="4">4 - Very Good</option>
                <option value="5">5 - Excellent</option>
            </select><br><br>
        </div>
    <?php endforeach; ?>

    <button type="submit" class="btn">Submit Review</button>
</form>

</body>
</html>
