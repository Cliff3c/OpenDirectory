<?php
    // Check if a session is not already active before starting one
    if (session_status() === PHP_SESSION_NONE) {
        session_start(); // Start the session if it's not already started
    }

    // Include function to check initialization status from config.json
    include "functions/get_init_status.php";

    // Check initialization status before loading the login page
    if (getInitializationStatus() === 0) {
        header("Location: initialize.php");
        exit; // Terminate script to ensure the redirect takes effect
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Page</title>
    <link rel="stylesheet" type="text/css" href="../css/admin.css">
</head>
<body>
    <h1 class="page-title">Admin Login Page</h1>
    <div class="container small">
        <form method="POST">
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required><br><br>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>
            <input type="submit" class="button" value="Login">
        </form>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Process form submission here
            $username = $_POST["username"];
            $password = $_POST["password"];

            // Read the users.json file and decode it
            $usersFile = file_get_contents('users/users.json');
            $usersData = json_decode($usersFile, true);

            // Check if the provided username exists in the JSON data
            if (array_key_exists($username, $usersData)) {
                // Verify the user-provided password against the stored hashed password
                if (password_verify($password, $usersData[$username]["password"])) {
                    // Authentication successful
                    // Set a session variable to indicate that the user is logged in
                    session_start();
                    $_SESSION['loggedin'] = true;
                    $_SESSION['username'] = $username; // You can store the username in the session for future use
					$_SESSION['user_role'] = $usersData[$username]["user_role"]; // Store the user role

                    // Set the timestamp for session expiration (1 hour)
                    $_SESSION['last_activity'] = time();

                    // Redirect to admin.php
                    header("Location: admin.php");
                    exit; // Terminate script to ensure the redirect takes effect
                } else {
                    // Authentication failed (incorrect password)
                    echo '<p class="error">Login failed. Invalid password.</p>';
                }
            } else {
                // Authentication failed (username not found)
                echo '<p class="error">Login failed. Invalid username.</p>';
            }
        }
        ?>
    </div>
</body>
</html>
