<?php
session_start();
include 'includes/db_connect.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Get the user ID from the URL
$user_id = $_GET['id'] ?? null;

if (!$user_id) {
    header("Location: admin_dashboard.php");
    exit;
}

// Fetch the user data from the database
$stmt = $pdo->prepare("SELECT UserID, Name, Email, Role FROM users WHERE UserID = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: admin_dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the updated user information
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);
    
    // Validate input
    if (empty($name) || empty($email) || empty($role)) {
        $error = "All fields are required!";
    } else {
        // Update the user in the database
        $stmt = $pdo->prepare("UPDATE users SET Name = :name, Email = :email, Role = :role WHERE UserID = :user_id");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        // Redirect back to the admin dashboard
        header("Location: admin_dashboard.php?success=UserUpdated");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Edit User: <?php echo htmlspecialchars($user['Name']); ?></h1>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="edit_user.php?id=<?php echo $user['UserID']; ?>" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['Name']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>" required>

            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="user" <?php echo ($user['Role'] === 'user') ? 'selected' : ''; ?>>User</option>
                <option value="company" <?php echo ($user['Role'] === 'company') ? 'selected' : ''; ?>>Company</option>
            </select>

            <button type="submit">Update User</button>
        </form>
    </div>
</body>
</html>
