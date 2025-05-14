<?php
// Start the session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set the default theme to 'light' if no theme is set
if (!isset($_SESSION['theme'])) {
    $_SESSION['theme'] = 'light';
}

// Toggle the theme if the toggle button is clicked
if (isset($_GET['toggle_theme'])) {
    $_SESSION['theme'] = $_SESSION['theme'] === 'light' ? 'dark' : 'light';
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?')); // Avoid repeated toggles on refresh
    exit();
}

// Fetch the number of items in the cart (assuming you store it in the session)
$cart_items_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoSwap</title>

    <!-- Load the appropriate stylesheet based on the theme -->
    <?php if ($_SESSION['theme'] === 'dark'): ?>
        <link rel="stylesheet" href="css/dark.css">
    <?php else: ?>
        <link rel="stylesheet" href="css/style.css">
    <?php endif; ?>

    <link rel="stylesheet" href="css/header.css"> <!-- Header styles -->
</head>
<body class="<?php echo $_SESSION['theme']; ?>"> <!-- Add the theme class here -->
<header class="<?php echo $_SESSION['theme']; ?>"> <!-- Add the theme class here -->
    <nav>
        <ul class="navbar">
            <!-- Default Home link -->
            <li><a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Home</a></li>
            
            <!-- Show links for all users -->
            <li><a href="products.php" class="<?= basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">Shop Now</a></li>
            <li><a href="contact.php" class="<?= basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>">Contact Us</a></li>
            <li><a href="about.php" class="<?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>">About Us</a></li>

            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Links for logged-in users -->
                <?php if ($_SESSION['role'] == 'company'): ?>
                    <li><a href="company_dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'company_dashboard.php' ? 'active' : ''; ?>">Profile</a></li>
                <?php else: ?>
                    <li><a href="user_dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'user_dashboard.php' ? 'active' : ''; ?>">Profile</a></li>
                <?php endif; ?>
                <li><a href="logout.php" class="<?= basename($_SERVER['PHP_SELF']) == 'logout.php' ? 'active' : ''; ?>">Logout</a></li>
            <?php else: ?>
                <!-- Links for guests -->
                <li><a href="login.php" class="<?= basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>">Login</a></li>
                <li><a href="register.php" class="<?= basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active' : ''; ?>">Sign Up</a></li>
            <?php endif; ?>

            <!-- Cart Button -->
            <li><a href="cart.php" class="cart-btn">
                Cart (<?php echo $cart_items_count; ?>)
            </a></li>

            <!-- Theme toggle button
            <li>
                <a href="?toggle_theme=1" class="btn-toggle" aria-label="Toggle theme">
                    <!-- <?php echo $_SESSION['theme'] === 'dark' ? 'Light Mode' : 'Dark Mode'; ?> -->
                </a>
            </li> 
        </ul>
        
        <!-- Hamburger menu for mobile -->
        <div class="hamburger" onclick="toggleMobileMenu()">
            <div></div>
            <div></div>
            <div></div>
        </div>
        <!-- Mobile Navigation -->
        <ul class="navbar-mobile" id="mobileMenu">
            <li><a href="index.php">Home</a></li>
            <li><a href="products.php">Shop Now</a></li>
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="about.php">About Us</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['role'] == 'company'): ?>
                    <li><a href="company_dashboard.php">Profile</a></li>
                <?php else: ?>
                    <li><a href="user_dashboard.php">Profile</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Sign Up</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <!-- Page content goes here -->
</main>

<script>
// Function to toggle the mobile menu visibility
function toggleMobileMenu() {
    const mobileMenu = document.getElementById('mobileMenu');
    mobileMenu.classList.toggle('open');
}
</script>

</body>
</html>
