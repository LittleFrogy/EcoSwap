<?php
session_start();

// Define a constant for the login page URL, or fetch from a config
define('LOGIN_PAGE', 'login.php');

// Regenerate session ID to prevent session fixation attacks
if (session_id()) {
    session_regenerate_id(true);
}

// Check if user is logged in by verifying the session variable 'user_id'
if (!isset($_SESSION['user_id'])) {
    // Store a message in the session to display to the user
    $_SESSION['error_message'] = 'You need to log in to access this page.';
    
    // Redirect to the login page
    header('Location: ' . LOGIN_PAGE);
    exit;
}

// Optionally, ensure that session cookie settings are secure (e.g., HttpOnly, Secure)
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), session_id(), time() + 3600, $params['path'], $params['domain'], isset($_SERVER["HTTPS"]), true);
}
?>
