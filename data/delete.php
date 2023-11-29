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
    <title>Delete JSON Files</title>
    <link rel="stylesheet" type="text/css" href="../css/admin.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Function to load JSON data for the selected file
            function loadJSONData(fileToView) {
                if (fileToView) {
                    $.ajax({
                        type: "POST",
                        url: "view.php", // Create a separate PHP file for viewing JSON data
                        data: { fileToView: fileToView },
                        success: function (data) {
                            $("#jsonView").html(data);
                        },
                    });
                }
            }

            // Trigger data loading when the selection changes
            $("#fileToDelete").change(function () {
                var fileToView = $(this).val();
                loadJSONData(fileToView);
            });

            // Initial load of data
            var initialFileToView = $("#fileToDelete").val();
            loadJSONData(initialFileToView);
        });
    </script>
</head>
<body>
		<h1 class="page-title">Delete JSON Files</h1>
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

        <!-- Popup overlay and container for instructions -->
        <div class="overlay" id="popupOverlay">
            <div class="popup">
                <div class="popup-content">
                    <!-- Popup content goes here -->
                    <p><b>INSTRUCTIONS:</b></p>
                    <ol>
                        <li>Select an employee from the dropdown list.</li>
                        <li>Review the employee data to confirm your selection.</li>
                        <li>Click on 'Delete JSON and Picture' to remove the employee data.</li>
                        <li>The user data will be removed from the live site.</li>
						<li>The page will redirect to the Admin Menu page. </li>
						
                    </ol>
                </div>
                <center><button id="closeButton">Close</button></center>
            </div>
        </div>
		
			<form method="post" action="">
				<label for="fileToDelete">Select JSON File to Delete:</label>
				<select name="fileToDelete" id="fileToDelete" required>
				<option value="" disabled selected>Select Name...</option> <!-- Default option -->
					<?php
					// Function to get employee names from JSON files
					function getEmployeeNames() {
						$jsonFiles = glob('employees/*.json');
						$employeeNames = [];

						foreach ($jsonFiles as $jsonFile) {
							$jsonData = json_decode(file_get_contents($jsonFile), true);
							if (isset($jsonData['name']['first']) && isset($jsonData['name']['last'])) {
								$firstName = $jsonData['name']['first'];
								$lastName = $jsonData['name']['last'];
								$employeeNames["$lastName, $firstName"] = $jsonFile; // Store name as key and file path as value
							}
						}

						// Sort employee names alphabetically by last name
						ksort($employeeNames);

						return $employeeNames;
					}

					$employeeNames = getEmployeeNames();

					// Generate dropdown options from the employeeNames array
					foreach ($employeeNames as $name => $filePath) {
						echo "<option value='$filePath'>$name</option>";
					}
					?>
				</select>

				<button type="button" id="viewButton" style="display: none;"></button>
				<div class="container-left" style="text-align: left;">
					<div id="jsonView"></div>
				</div>
				<input type="submit" name="delete" value="Delete JSON and Picture">
			</form>

			<?php
			// PHP code for handling file deletion
			if (isset($_POST['delete'])) {
				$fileToDelete = $_POST['fileToDelete'];
				$filePath = $fileToDelete;

				if (file_exists($filePath)) {
					// Load the JSON data from the selected file
					$jsonContent = file_get_contents($filePath);
					$jsonData = json_decode($jsonContent, true);

					if ($jsonData !== null) {
						// Extract image file paths from the JSON data
						if (isset($jsonData['picture']) && is_array($jsonData['picture'])) {
							$picturePaths = $jsonData['picture'];
							foreach ($picturePaths as $size => $picturePath) {
								// Normalize the path to use forward slashes
								$normalizedPath = str_replace("\\", "/", $picturePath);
								
								// Extract the last part of the path (the filename)
								$parts = explode("/", $normalizedPath);
								$filename = end($parts);
								
								// Convert both filenames to lowercase and compare
								if (strtolower($filename) === "blank.jpg") {
									continue; // Skip deletion if the image filename is "blank.jpg"
								}
								
								// Delete image file if it exists
								$imagePath = "../" . $normalizedPath;
								if (file_exists($imagePath)) {
									unlink($imagePath);
								}
							}
						}						

						// Delete the JSON file
						if (unlink($filePath)) {
							echo "<p class='success'>File '$fileToDelete' deleted successfully.</p>";
							include 'update_data.php';
							echo "<script>
								setTimeout(function() {
									window.location.href = 'admin.php';
								}, 3000); // Redirect after a 3-second delay (adjust the delay as needed)
							</script>";
						} else {
							echo "<p class='error'>Failed to delete file '$fileToDelete'.</p>";
						}
					} else {
						echo "<p class='error'>Failed to decode JSON data from the selected file.</p>";
					}
				} else {
					echo "<p class='error'>File '$fileToDelete' does not exist.</p>";
				}
			}
			?>
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