<?php
session_start();
include 'includes/db_connect.php'; // Include database connection

// Ensure user is logged in and has the role of 'user'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

// Handle form submission to update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $profile_image = $_FILES['profile_image'];

    // Update profile image if a new one is uploaded
    if ($profile_image['error'] === 0) {
        $image_name = $profile_image['name'];
        $image_tmp = $profile_image['tmp_name'];
        $image_path = "images/" . basename($image_name);

        // Move the uploaded image to the images directory
        if (move_uploaded_file($image_tmp, $image_path)) {
            $_SESSION['profile_image'] = $image_name; // Store in session
        }
    }

    // Update user data in the database
    $stmt = $pdo->prepare("UPDATE users SET Name = ?, phone = ?, address = ?, profile_image = ? WHERE UserID = ?");
    $stmt->execute([$name, $phone, $address, $_SESSION['profile_image'] ?? null, $_SESSION['user_id']]);

    // Update session data after profile change
    $_SESSION['Name'] = $name;
    $_SESSION['phone'] = $phone;
    $_SESSION['address'] = $address;

    // Redirect to dashboard after update
    header("Location: user_dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/edit_profile.css">
</head>
<body>
    <div class="edit-profile-container">
        <h1>Edit Profile</h1>
        <form action="edit_profile.php" method="post" enctype="multipart/form-data">
            <div>
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_SESSION['Name']); ?>" required>
            </div>
            <div>
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($_SESSION['phone'] ?? ''); ?>">
            </div>
            <div>
                <label for="address">Address:</label>
                <textarea id="address" name="address"><?php echo htmlspecialchars($_SESSION['address'] ?? ''); ?></textarea>
            </div>
            <div>
                <label for="profile_image">Profile Image:</label>
                <input type="file" id="profile_image" name="profile_image" accept="image/*">
            </div>
            <button type="submit">Save Changes</button>
        </form>
    </div>
</body>
</html>
