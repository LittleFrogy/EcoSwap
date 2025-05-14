<?php
session_start();
include 'includes/header.php';
?>
<link rel="stylesheet" href="css/payment_success.css">
<main class="container">
    <h1>Payment Successful</h1>
    <p>Your payment has been completed successfully. Thank you for your order!</p>
    <a href="user_dashboard.php" class="btn">Go to Dashboard</a>
</main>

<?php include 'includes/footer.php'; ?>