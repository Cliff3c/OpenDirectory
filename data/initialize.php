<?php
    // Check if a session is not already active before starting one
    if (session_status() === PHP_SESSION_NONE) {
        session_start(); // Start the session if it's not already started
    }
    // Check if the reset token exists and matches the submitted token
    if (!isset($_SESSION['initializeToken'])) {
        echo "<script>alert('Unauthorized access. Session token is not set. {$_SESSION['initializeToken']}');</script>";
        exit;
    }
    // Processing form data upon submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Logic to update conf/config.json with $pageTitle, and $deptNames
        $configFile = 'conf/config.json';
        $configData = json_decode(file_get_contents($configFile), true);

        // Extract Form Data
        $pageTitle = $_POST["pageTitle"];
        $deptNames = $_POST["deptNames"]; // Array of department names

        // Update Page Title
        $configData["pageTitle"] = $pageTitle;

        // Add Department Names
        $configData["organization_Settings"]["Departments"] = $deptNames; // Assign the sorted array directly

        // Add predefined titles
        // Static prefix titles
        $staticPrefixTitles = ["Miss", "Mr.", "Mrs.", "Ms."];

        // Assign static titles to the configuration data
        $configData["organization_Settings"]["titles"] = [];

        foreach ($staticPrefixTitles as $index => $title) {
            $configData["organization_Settings"]["titles"]["title_" . $index] = $title;
        }

        // Update Initialization Status
        $configData["initialize_status"] = "1";

        // Save updated data back to config.xml
        file_put_contents($configFile, json_encode($configData, JSON_PRETTY_PRINT));

        // Root Password Handling
        if (isset($_POST['new_password']) && isset($_POST['confirm_new_password'])) {
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_new_password'];
    
            if ($newPassword === $confirmPassword) {
                // Hash the password securely
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
                // Create data for the root user
                $userData = [
                    "root" => [
                        "password" => $hashedPassword,
                        "user_role" => "super_admin"
                    ]
                ];
    
                // Store the user data in users.json
                file_put_contents('users/users.json', json_encode($userData, JSON_PRETTY_PRINT));
            } else {
                // Passwords do not match
                echo '<p class="error">Passwords do not match. Please try again.</p>';
            }
        }
        
        header("Location: login.php");
        exit;
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Initialization</title>
    <link rel="stylesheet" type="text/css" href="../css/admin.css">
    <link rel="stylesheet" type="text/css" href="../css/settings.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1 class="page-title">Initialize Employee Directory</h1>
    <div class="container large">
        <center><button id="toggleButton" class="toggle-button">Toggle Instructions</button></center>
        <!-- JavaScript for toggling the instructions popup -->
		<script>
			document.addEventListener("DOMContentLoaded", function () {
				const toggleButton = document.getElementById("toggleButton");
				const popupOverlay = document.getElementById("popupOverlay");
				const closeButton = document.getElementById("closeButton");

				toggleButton.addEventListener("click", function () {
					popupOverlay.style.display = "block";
				});

				closeButton.addEventListener("click", function () {
					popupOverlay.style.display = "none";
				});
			});
		</script>

        <!-- JavaScript for toggling the password view checkbox -->
        <script>
            function showPassword() {
                const newPasswordInput = document.querySelector('input[name="new_password"]');
                const confirmNewPasswordInput = document.querySelector('input[name="confirm_new_password"]');
                const showPasswordCheckbox = document.getElementById('show_password');
        
                if (showPasswordCheckbox.checked) {
                    newPasswordInput.type = 'text';
                    confirmNewPasswordInput.type = 'text';
                } else {
                    newPasswordInput.type = 'password';
                    confirmNewPasswordInput.type = 'password';
                }
            }
        </script>

        <!-- Popup overlay and container for instructions -->
        <div class="overlay" id="popupOverlay">
            <div class="popup">
                <div class="popup-content">
                    <!-- Popup content goes here -->
                    <p><b>INSTRUCTIONS:</b></p>
                    <ol>
                        <li>Complete all the necessary fields in the form below.</li>
                        <li>Include the department(s) associated with your organization. Use the 'Add Department' button to generate additional input fields and ensure you can add all departments relevant to your organization.</li>
                    </ol>
                </div>
                <center><button id="closeButton">Close</button></center>
            </div>
        </div>
        <form method="post" action="initialize.php">
            <div class="form-group">
                <label for="newPassword">Set Root Password:</label>
                <p style="font-size: 11px;">Please set a root user password.</p>            
                <input type="password" name="new_password" placeholder="New Password" required><br>
                <input type="password" name="confirm_new_password" placeholder="Confirm New Password" required><br>
                <label for="show_password">
                    <input type="checkbox" id="show_password" onclick="showPassword()">
                    Show Password
                </label>
            </div>
            <hr>
            <div class="form-group">
                <label for="pageTitle">Directory Name:</label>
                <p style="font-size: 11px;">Please provide a directory name that will be viewed on the index page.</p>
                <input type="text" name="pageTitle" id="pageTitle" placeholder="Enter a title to be displayed for the contact directory..." required>
            </div>
            <hr>
            <div class="form-group" id="deptNamesContainer">
                <label for="deptNames">Department Names</label>
                <p style="font-size: 11px;">Enter department names associated with your organization. Feel free to add as many departments as needed.</p>
                <div id="departmentInputs">
                    <div class="department">
                        <div class="input-with-remove">
                            <input type="text" name="deptNames[]" class="deptName" placeholder="Add a department name..." required>
                            <button type="button" class="button2 removeDepartment" style="display: none;">Remove</button>
                        </div>
                    </div>
                </div>
                <button type="button" class="addDepartment">Add Department</button>
            </div>

            <script>
                $(document).ready(function () {
                    let departmentCount = 1; // Initial department input count

                    $('.addDepartment').click(function () {
                        departmentCount++; // Increment count
                        const newDepartment = `
                            <div class="department">
                                <div class="input-with-remove">
                                    <input type="text" name="deptNames[]" class="deptName" placeholder="Add a department name..." required>
                                    <button type="button" class="button2 removeDepartment">Remove</button>
                                </div>
                            </div>
                        `;
                        $('#departmentInputs').append(newDepartment);

                        // Hide Remove button for the first department field
                        $('#departmentInputs .department:first-child .removeDepartment').hide();
                    });

                    $('#departmentInputs').on('click', '.removeDepartment', function () {
                        if (departmentCount > 1) {
                            $(this).closest('.department').remove();
                            departmentCount--;
                        } else {
                            alert("At least one department field is required.");
                        }
                    });
                });            
            </script>
            <button type="submit">Submit</button>
        </form>
    </div> 
</body>
</html>   

