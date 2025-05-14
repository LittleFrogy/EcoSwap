<?php
session_start();
include 'includes/db_connect.php';  // Include database connection

// Get user input from form and sanitize
$username = htmlspecialchars(trim($_POST['username']));
$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
$password = trim($_POST['password']);
$confirm_password = trim($_POST['confirm_password']);
$role = htmlspecialchars(trim($_POST['role'])); // Sanitize role input

// Validate the input
if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
    header("Location: register.php?error=All fields are required");
    exit;
}

// Check if the password and confirm password match
if ($password !== $confirm_password) {
    header("Location: register.php?error=Passwords do not match");
    exit;
}

// Check if the email already exists in the database
$stmt = $pdo->prepare("SELECT UserID FROM users WHERE email = :email");
$stmt->bindParam(':email', $email);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // If the email already exists, redirect with an error message
    header("Location: register.php?error=EmailExists");
    exit;
}

// Restrict invalid roles
$allowed_roles = ['user', 'company']; // Only 'user' and 'company' allowed roles
if (!in_array($role, $allowed_roles)) {
    // Allow admin registration only if an admin is logged in
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: register.php?error=InvalidRole");
        exit;
    }
}

// Hash the password before saving it
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert the new user into the database
$stmt = $pdo->prepare("INSERT INTO users (Name, Email, Password, Role) VALUES (:username, :email, :password, :role)");
$stmt->bindParam(':username', $username);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':password', $hashed_password);
$stmt->bindParam(':role', $role);
$stmt->execute();

// Redirect to login page after successful registration
header("Location: login.php?success=RegistrationSuccess");
exit;
?>
