<?php
session_start();
include 'includes/db_connect.php';  // Include database connection

// Get user input from form
$email = trim($_POST['email']);
$password = trim($_POST['password']);

// Validate input
if (empty($email) || empty($password)) {
    header("Location: login.php?error=All fields are required");
    exit;
}

// Check if the email exists in the database
$stmt = $pdo->prepare("SELECT UserID, Name, Email, Password, Role FROM users WHERE email = :email");
$stmt->bindParam(':email', $email);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // Verify the password
    if (password_verify($password, $user['Password'])) {
        // Regenerate the session ID to prevent session fixation
        session_regenerate_id(true);

        // Store user information in the session
        $_SESSION['user_id'] = $user['UserID'];
        $_SESSION['username'] = $user['Name'];
        $_SESSION['email'] = $user['Email'];
        $_SESSION['role'] = $user['Role'];

        // Debugging: Check session data
        var_dump($_SESSION); // Remove after debugging

        // Redirect to the appropriate dashboard
        if ($user['Role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } elseif ($user['Role'] === 'company') {
            header("Location: company_dashboard.php");
        } else {
            header("Location: user_dashboard.php");
        }
        exit;
    } else {
        // Invalid password
        header("Location: login.php?error=InvalidPassword");
        exit;
    }
} else {
    // Email not found
    header("Location: login.php?error=EmailNotFound");
    exit;
}
?>
