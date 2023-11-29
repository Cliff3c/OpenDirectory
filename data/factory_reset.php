<!DOCTYPE html>
<html lang="en">
<head>
    <title>Factory Reset</title>
    <!-- Other head elements such as meta tags, stylesheets, and scripts -->
</head>
<body>
<?php
    session_start();
    foreach ($_SESSION as $key => $value) {
        echo $key . ' = ' . $value . '<br>';
    }
    // Check if the reset token exists and matches the submitted token
    if (!isset($_SESSION['resetToken'])) {
        echo "<script>alert('Unauthorized access. Session token is not set.');</script>";
        exit;
    }
        
        $response = ["success" => false, "message" => ""];

        // Step 1: Clear out any data that may currently exist
        $directoriesToClear = ['conf/', 'employees/', '../images/large/', '../images/med/', '../images/thumb/', 'users/'];
        foreach ($directoriesToClear as $directory) {
            $files = glob($directory . '*'); // Get all files within the directory
        
            foreach ($files as $file) {
                if (is_file($file)) {
                    // Check if the file is 'blank.jpg' in the specified directories
                    if (
                        ($directory === '../images/large/' || $directory === '../images/med/' || $directory === '../images/thumb/')
                        && basename($file) === 'blank.jpg'
                    ) {
                        continue; // Skip deletion for 'blank.jpg'
                    }
        
                    if (!unlink($file)) {
                        $response["message"] .= "Failed to delete file: $file. ";
                    }
                }
            }
        }

        // Step 2: Create necessary files with JSON data
        // Create conf/config.json file
        $configData = [
            "initialize_status" => "0",
            "pageTitle" => "EMPLOYEE DIRECTORY",
            "organization_Settings" => [
                "titles" => [
                    "title_0" => "Miss",
                    "title_1" => "Mr.",
                    "title_2" => "Mrs.",
                    "title_3" => "Ms."
                ],
                "Departments" => []
            ]
        ];
        file_put_contents('conf/config.json', json_encode($configData, JSON_PRETTY_PRINT));

        // Create users.json file in the "users/" folder
        $password = password_hash('password', PASSWORD_DEFAULT);
        $usersData = [
            "root" => [
                "password" => $password,
                "user_role" => "super_admin"
            ]
        ];
        file_put_contents('users/users.json', json_encode($usersData, JSON_PRETTY_PRINT));

        // Create employees.json file
        $employeesData = ["results" => []];
        file_put_contents('employees.json', json_encode($employeesData, JSON_PRETTY_PRINT));

        // Step 3: Logout the current session
        echo "<script>alert('The site has been reset to factory defaults. You will now be logged out.'); window.location.href = 'logout.php';</script>";
    ?>
</body>
</html>
