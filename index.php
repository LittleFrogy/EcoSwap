<?php
// Start the session
session_start();

// Include the header (navbar, etc.)
include 'includes/header.php';
?>

<link rel="stylesheet" href="css/index.css">

<main class="container">

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Welcome to <span class="brand-name">EcoSwap</span></h1>
            <p>Sell, exchange, or discover recycled and pre-loved products. Join us in creating a sustainable future by reusing and reducing waste!</p>

            <?php if (isset($_SESSION['user_id'])): ?>
                <p class="welcome-message">Welcome back, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</p>
                <div class="cta-buttons">
                    <a href="products.php" class="btn primary-btn">Browse Products</a>
                    <a href="add_product.php" class="btn secondary-btn">Sell a Product</a>
                </div>
            <?php else: ?>
                <div class="cta-buttons">
                    <a href="login.php" class="btn primary-btn">Login</a>
                    <a href="register.php" class="btn secondary-btn">Join Now</a>
                </div>
            <?php endif; ?>
        </div>
        <div class="hero-image">
            <img src="images\vector-icon-set-recyclable-materials-260nw-248750854.jpg" alt="Recycle and exchange unused products" loading="lazy">
        </div>
    </section>

    <!-- Search Section -->
    <section class="search-section">
        <form class="search-form" action="search.php" method="GET" aria-label="Search for products">
            <input type="text" name="query" placeholder="Find products to buy, sell, or exchange..." aria-label="Search input" />
            <button type="submit" class="btn search-btn" aria-label="Submit search">Search</button>
        </form>
    </section>

    <!-- About Us Section -->
    <section class="about-us">
        <h2>About <span class="brand-name">EcoSwap</span></h2>
        <p>At EcoSwap, we believe in reducing waste and promoting sustainability. Whether you’re selling items you no longer need, buying affordable second-hand goods, or exchanging products with others, every action makes a difference for our planet.</p>
    </section>

    <!-- Features Section -->
    <section class="features">
        <h2>Why Choose <span class="brand-name">EcoSwap</span>?</h2>
        <div class="features-grid">
            <div class="feature-item">
                <img src="images\tosell3-b-680.jpg" alt="Sell Unused Items" loading="lazy">
                <h3>Sell Unused Items</h3>
                <p>Clear out your clutter and earn money by selling items you no longer need.</p>
            </div>
            <div class="feature-item">
                <img src="images\images.jpeg" alt="Exchange Products" loading="lazy">
                <h3>Exchange Products</h3>
                <p>Trade products with others to find exactly what you need without spending extra money.</p>
            </div>
            <div class="feature-item">
                <img src="images\buy recycle goods.jpeg" alt="Recycled Products" loading="lazy">
                <h3>Buy Recycled Goods</h3>
                <p>Shop for high-quality recycled products and contribute to a sustainable economy.</p>
            </div>
        </div>
    </section>
</main>
<script src="js/dark_mode.js"></script>
<?php
// Include the footer
include 'includes/footer.php';
?>
