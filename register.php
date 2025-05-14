<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/register.css">
</head>
<body>

    <div class="container">
        <h2>Create an Account</h2>

        <form action="register_process.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required placeholder="Enter username">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="Enter email">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter password">
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm password">
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="user">User</option>
                    <option value="company">Company</option>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
                        <option value="admin">Admin</option>
                    <?php endif; ?>
                </select>
            </div>

            <button type="submit">Register</button>
        </form>

        <!-- Display error message if available -->
        <?php if (isset($_GET['error'])) : ?>
            <p class="error-message"><?php echo $_GET['error']; ?></p>
        <?php endif; ?>

        <!-- Display success message if registration is successful -->
        <?php if (isset($_GET['success'])) : ?>
            <p class="success-message"><?php echo $_GET['success']; ?></p>
        <?php endif; ?>

        <div class="login-link">
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </div>
    </div>

</body>
</html>
