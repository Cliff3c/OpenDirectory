<?php
// Start the session
session_start();

// Check if the user is logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // User is logged in; destroy the session
    session_destroy();
}

// Redirect to the login page after logout
header("Location: login.php");
exit; // Terminate script to ensure the redirect takes effect
?>
