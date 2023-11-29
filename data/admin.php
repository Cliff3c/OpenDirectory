<?php
// Start or resume the session
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // User is not logged in; redirect to the login page or display an access denied message
    header("Location: ../data/login.php"); // Redirect to the login page
    exit; // Terminate script to ensure the redirect takes effect
} else {
    // Check for session activity timeout
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 3600)) {
        // If more than 1 hour has passed, destroy the session
        session_unset();
        session_destroy();
        header("Location: ../data/login.php"); // Redirect to login page
        exit;
    } else {
        // Update the last activity timestamp
        $_SESSION['last_activity'] = time();
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Admin Page</title>
    <link rel="stylesheet" type="text/css" href="../css/admin.css">
</head>
<body>
    <h1 class="page-title">Admin Page</h1>
    <div class="container small">
		<?php include 'check_reviewstatus.php'; ?>
        <ul>
            <li><a class="button" title="Directory will open in new tab." href="../index.php" target="_blank">View Employee Directory</a></li>
            <li><a class="button" title="Add an employee to the directory." href="../data/json_gen.php">Add Employee</a></li>
            <li><a class="button" title="Remove an employee from the directory." href="../data/delete.php">Delete Employee</a></li>
            <li><a class="button" title="Change an existing employees information." href="../data/edit.php">Modify Employee</a></li>
			<li><a class="button" title="Change an existing employees picture." href="../data/editpic.php">Change Picture</a></li>
			<li><a class="button" title="Manage system users." href="../data/user_management.php">Manage Users</a></li>
			<?php if ($_SESSION['user_role'] === 'super_admin'): ?>
                <li><a class="button" title="Change system settings." href="../data/settings.php">Settings</a></li>
                <li><a class="button" title="Backup, restore, or factory reset the site data." href="../data/backup.php">Backup / Restore / Reset</a></li>
            <?php endif; ?>
			<li><a class="button" title="Review documentation of site functionality." href="../data/help.php">Help</a></li>
        </ul>

    </div>
	<div class="footer">
		<div class="logout-button">
			<a class="button" href="logout.php">Logout</a>
		</div>
		<div class="user-info">
			Username: <?php echo $_SESSION['username']; ?>
		</div>		
	</div>

</body>
</html>
