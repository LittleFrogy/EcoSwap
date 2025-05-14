<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

    <div class="login-container">
        <div class="login-box">
            <h2>Login</h2>

            <form action="login_process.php" method="POST">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required aria-label="Email Address">

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required aria-label="Password">
                
                <!-- Password Visibility Toggle -->
                <div class="password-toggle">
                    <input type="checkbox" id="toggle-password" onclick="togglePasswordVisibility()">
                    <label for="toggle-password">Show Password</label>
                </div>

                <button type="submit">Login</button>
            </form>

            <!-- Display error or success messages -->
            <?php if (isset($_GET['error'])) : ?>
                <p class="error-message"><?php echo $_GET['error']; ?></p>
            <?php endif; ?>

            <?php if (isset($_GET['success'])) : ?>
                <p class="success-message"><?php echo $_GET['success']; ?></p>
            <?php endif; ?>

            <div class="register-link">
                <p>Don't have an account? <a href="register.php">Register here</a>.</p>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordField = document.getElementById("password");
            const toggle = document.getElementById("toggle-password");
            passwordField.type = toggle.checked ? "text" : "password";
        }
    </script>

</body>
</html>
