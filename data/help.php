<?php
	// Start the session
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
    <title>Admin Help Page</title>
    <link rel="stylesheet" type="text/css" href="../css/admin.css">
    <style>
        ul {
            list-style-type: disc; /* This will display bullets for list items */
            margin-left: 30px; /* Adjust the indentation as needed */
        }

        ul ul {
            margin-left: 30px; /* Nested unordered lists */
        }
    </style>
</head>
<body>
    <h1 class="page-title">Admin Page Help</h1>
    <div class="container large">
        <center><h3>High Level Overview of the Site</h3></center>
        <p>Welcome to the Admin Page! This page provides various functions for managing employee data. Below, you'll find explanations for each of the links and buttons available on this page:</p>
        
        <ol>
            <a class="button" title="Click to view detailed documentation." href="help/details.php">Click for Detailed Documentation</a><br>
            <li>
                <strong>View Employee Directory:</strong><br>
                Clicking this button opens a new tab displaying the employee directory. The directory provides an overview of all employees and their details, including name, position, and contact information.
            </li>
            <li>
                <strong>Add Employee:</strong><br>
                This link takes you to a page where you can add a new employee to the database. You'll be prompted to enter the employee's information, such as name, position, and contact details. After adding an employee, you are redirected to the 'Apply Updates' page to apply changes. You can choose to return to this Admin Page by clicking the 'Back to Admin' button.
            </li>
            <li>
                <strong>Delete Employee:</strong><br>
                Clicking this link navigates to a page where you can delete an existing employee from the database. You will typically be prompted to select the employee you want to remove. Be cautious when using this function, as it permanently removes employee data by deleting the JSON file associated to the selected employee. You will be redirected to the 'Apply Updates' page in order to apply the changes made to the live site.
            </li>
            <li>
                <strong>Modify Employee:</strong><br>
                This link directs you to a page where you can make changes to an employee's information. You'll likely need to select the employee you wish to modify, make the necessary edits, and save the updated information. You will be redirected to the 'Apply Updates' page in order to apply the changes made to the live site.
            </li>
            <li>
                <strong>Change Picture:</strong><br>
                This brings you to a page where you can select any existing employee in the system and allows you to update their picture. Images must be JPEG or PNG images and it is suggested they have a 1:1 ratio to prevent image distortion. Images can also be set to the default 'blank' picture. 
            </li>
            <li>
                <strong>Manage Users:</strong><br>
                This page differs based upon user permissions. Users can simply change their own password. 'Super_admins' can register new users, set user roles, delete users, change passwords for any user, and view a list of current users and their permission level.
            </li>
            <li>
                <strong>Settings:</strong><br>
                This page is for 'super_admin' users and provide functionality to view initialization status, view and change the current page title, prefix titles, and/or registered departments within the organization.
            </li>
            <li>
                <strong>Backup / Restore / Reset:</strong><br>
                This page gives a 'super_admin' user the ability to create a backup, restore a backup, or run a factory reset wiping all data from the site bringing it back to an 'out of box experience.'
                <ul>
                    <li>
                        The backup generates a zip file that is automatically named 'backup' with the current UTC time appended to the file name.
                    </li>
                    <li>
                        The restore functionality checks the zip file and confirms compatibility prior to applying it to the site. (<strong><a href="../sample/SampleData.zip">Download Sample Data</a></strong>)
                    </li>
                    <li>
                        The factory reset functionality wipes all data and prompts for the site to be initialized again in which a root password is set, a page title is applied, and one or more departments within the organization are created.
                    </li>
                </ul>
            </li>

            <li>
                <strong>Help:</strong><br>
                This brings the user to this page where a 'high level overview' of the site is provided. If the user wishes, they can click the blue button for detailed documentation about the site and the code.
            </li>
            <li>
                <strong>Logout:</strong><br>
                Clicking this link will log you out of the Admin Page and is located as the footer button.
            </li>
            <li>
                <strong>Toggle Instructions (Optional):</strong><br>
                If available, you can use this toggle button to reveal or hide additional instructions for using this page. These instructions may provide guidance on using specific features or functions. You can see this on the 'Apply Update' page. 
            </li>
        </ol>
    </div>
    <div class="footer">
        <div class="logout-button">
			<a href="../data/admin.php" class="button">Back to Admin</a>
		</div>
		<div class="user-info">
			Username: <?php echo $_SESSION['username']; ?>
		</div>		
	</div>
</body>
</html>
