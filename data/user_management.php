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
	
	// Function to load user data from the users.json file
	function loadUserData() {
		$userDataFile = '../data/users/users.json';
		if (file_exists($userDataFile)) {
			$userData = json_decode(file_get_contents($userDataFile), true);
			if ($userData !== null) {
				return $userData;
			}
		}
		return []; // Return an empty array if there's an issue with loading the data
	}

	// Load user data at the beginning of the script
	$userData = loadUserData();

	// Function to save user data to the users.json file
	function saveUserData($userData) {
		$userDataFile = '../data/users/users.json';
		file_put_contents($userDataFile, json_encode($userData, JSON_PRETTY_PRINT), LOCK_EX);
	}

	// Process changing a user's password
	if (isset($_POST['change_password'])) {
		$userToChangePassword = $_POST['user_to_change_password'];
		$newPassword = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
		if (array_key_exists($userToChangePassword, $userData)) {
			$userData[$userToChangePassword]['password'] = $newPassword;
			saveUserData($userData);
		} else {
			echo '<p class="error">User not found.</p>';
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Management</title>
    <link rel="stylesheet" type="text/css" href="../css/admin.css">
</head>
<body>
	<h1 class="page-title">User Management</h1>
    <div class="container small">
		<?php						
			   // Process user registration
			$registrationError = '';
			if (isset($_POST['register'])) {
				$username = strtolower($_POST['username']); // Convert the input username to lowercase

				// Check if the username already exists
				if (array_key_exists($username, $userData)) {
					$registrationError = 'User was not created - that username already exists. Please register a user with a unique username.';
				} else {
					$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
					$userRole = ($_SESSION['user_role'] === 'super_admin' && isset($_POST['user_role'])) ? $_POST['user_role'] : 'user';
					$userData[$username] = ['password' => $password, 'user_role' => $userRole];
					saveUserData($userData);
				}
			}

		?>
		
		<?php if (!empty($registrationError)): ?>
				<div class="warning-message">
					<p class="error"><?= $registrationError ?></p>
				</div>
		<?php endif; ?>

        <?php if ($_SESSION['user_role'] === 'super_admin'): ?>
            <details>
                <summary class="toggle-button">Register User</summary>
                <div class="instructions">
                    <form method="POST" action="">
                        <input type="text" name="username" placeholder="Username" required><br>
                        <input type="password" name="password" placeholder="Password" required><br>
                        <?php if ($_SESSION['user_role'] === 'super_admin'): ?>
                            <select name="user_role">
                                <option value="user">User</option>
                                <option value="super_admin">Super Admin</option>
                            </select><br>
                        <?php endif; ?>
                        <input type="submit" name="register" value="Register">
                    </form>					
                </div>
            </details>
        <?php endif; ?>
		
		<!-- Toggle button for deleting a user (for super_admin) -->
		<?php if ($_SESSION['user_role'] === 'super_admin'): ?>
			<details id="delete-user-details"> <!-- Add an ID here -->
				<summary class="toggle-button">Delete User</summary>
				<div class="instructions">
					<form method="POST" action="">
						<select id="user-to-delete" name="user_to_delete">
							<?php
							// Loop through your user data to generate options
							foreach ($userData as $user => $data) {
								if ($user !== 'root') {
									echo "<option value=\"$user\">$user</option>";
								}
							}
							?>
						</select><br>
						<input type="button" name="delete_user" value="Delete User" onclick="deleteUser()">
					</form>
					
					<script>
						// Function to update the user dropdown list
						function updateUserDropdown() {
							const userSelect = document.getElementById("user-to-delete");
							const selectedUser = userSelect.value;
							// No need to clear the existing options

							// Set the previously selected user, if it still exists
							userSelect.value = selectedUser;
						}

						// Event listener for updating the dropdown list
						const userSelect = document.getElementById("user-to-delete");
						userSelect.addEventListener("change", updateUserDropdown);

						// Function to delete the selected user
						function deleteUser() {
							const userSelect = document.getElementById("user-to-delete");
							const selectedUser = userSelect.value;

							// Send an AJAX request to delete the user
							fetch('deleteUser.php', {
								method: 'POST',
								body: new URLSearchParams({ delete_user: '', user_to_delete: selectedUser }),
							})
							.then(response => response.json())
							.then(data => {
								if (data.success) {
									alert('User deleted successfully');
									updateUserDropdown(); // Update the dropdown list
									setTimeout(function() {
										window.location.href = 'user_management.php'; // Refresh page by reloading it
									}, 100);
								} else {
									alert(data.message); // Display an error message
								}
							});
						}
					</script>
					
				</div>
			</details>
		<?php endif; ?>


        <?php if ($_SESSION['user_role'] === 'super_admin'): ?>
			<details>
				<summary class="toggle-button">Change User Password</summary>
				<div class="instructions">
					<form method="POST" action="" onsubmit="return validatePassword()">
						<select name="user_to_change_password">
							<?php foreach ($userData as $user => $data): 
								if ($_SESSION['username'] === 'root' || $user !== 'root'): ?>
									<option value="<?= $user ?>"><?= $user ?></option>
								<?php endif; ?>
							<?php endforeach; ?>
						</select><br>
						<input type="password" name="new_password" placeholder="New Password" required><br>
						<input type="password" name="confirm_new_password" placeholder="Confirm New Password" required><br>
						<input type="checkbox" id="show_password" onclick="showPassword()">
						<label for="show_password">Show Password</label><br>
						<input type="submit" name="change_password" value="Change Password">
					</form>
				</div>
			</details>
		<?php endif; ?>
  

        <?php if ($_SESSION['user_role'] === 'user'): ?>
            <details>
                <summary class="toggle-button">Change Your Password</summary>
                <div class="instructions">
                    <form method="POST" action="" onsubmit="return validatePassword()">
                        <input type="hidden" name="user_to_change_password" value="<?= $_SESSION['username'] ?>">
                        <input type="password" name="new_password" placeholder="New Password" required><br>
                        <input type="password" name="confirm_new_password" placeholder="Confirm New Password" required><br>
                        <input type="checkbox" id="show_password" onclick="showPassword()">
                        <label for="show_password">Show Password</label><br>
                        <input type="submit" name="change_password" value="Change Password">
                    </form>
                </div>
            </details>
        <?php endif; ?>

        <?php if ($_SESSION['user_role'] === 'super_admin'): ?>
            <details>
                <summary class="toggle-button">View Current Users</summary>
                <div class="instructions">
                    <table>
                        <thead>
                            <tr>Yes,
                                <th>Username</th>
                                <th>User Role</th>
                            </tr>
                        </thead>
                        <tbody id="user-table-body">
                            <?php foreach ($userData as $username => $data): ?>
                                <tr>
                                    <td><?= $username ?></td>
                                    <td><?= $data['user_role'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </details>
        <?php endif; ?>
    </div>
    <script>
        const toggleButtons = document.querySelectorAll('.toggle-button');

		toggleButtons.forEach(function(toggleButton) {
			toggleButton.addEventListener('click', function() {
				const instructions = this.nextElementSibling;

				if (instructions.style.display === 'none' || instructions.style.display === '') {
					instructions.style.display = 'block';
				} else {
					instructions.style.display = 'none';
				}
			});
		});
        
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
        
        function validatePassword() {
            const newPasswordInput = document.querySelector('input[name="new_password"]');
            const confirmNewPasswordInput = document.querySelector('input[name="confirm_new_password"]');
            const newPassword = newPasswordInput.value;
            const confirmNewPassword = confirmNewPasswordInput.value;
    
            if (newPassword !== confirmNewPassword) {
                alert("New Password and Confirm New Password must match.");
                return false;
            }
    
            return true;
        }
    </script>
    <div class="footer">
        <div class="logout-button">
            <a href="../data/admin.php" class="button">Back to Admin</a>
        </div>
        <div class="user-info">
            Username: <?= $_SESSION['username'] ?>
        </div>        
    </div>
</body>
</html>
