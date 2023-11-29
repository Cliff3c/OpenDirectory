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
	// Check if the "Generate JSON" button is clicked
	//if (isset($_POST['generate'])) {
	//	// Initialize a session variable to indicate that the user is starting the review process
	//	$_SESSION['employee_review_in_progress'] = true;    
	//}

	
	//// Get the reviewInProgress variable from the AJAX request
    $reviewInProgress = isset($_POST['reviewInProgress']) ? $_POST['reviewInProgress'] : false;
	//if ($reviewInProgress == true) {
	//	echo "Employee review in progress: " . ($_SESSION['employee_review_in_progress'] ? 'Yes' : 'No');
	//} else {
	//	echo "Employee review in progress: Not set";
	//}
	
	if (isset($_POST['fileToView'])) {
		$fileToView = $_POST['fileToView'];

		if (file_exists($fileToView)) {
			$jsonContent = file_get_contents($fileToView);
			$jsonData = json_decode($jsonContent, true);

			if ($jsonData !== null) {
				// Wrap JSON data and images in div elements with border class
				echo '<div class="border-box">';

				// Display the JSON data
				echo "<b>JSON Data:</b>";
				echo "<pre class='json-dataview'>" . json_encode($jsonData, JSON_PRETTY_PRINT) . "</pre>";

				// Display associated images if they exist
				if (isset($jsonData['picture']) && is_array($jsonData['picture'])) {
					echo '<label2>IMAGES:</label2>';
					echo '<div class="image-container">'; // Add the container
					foreach ($jsonData['picture'] as $size => $picturePath) {
						$isBlank = 0; // Variable tracking if the image is set to blank
					
						if (
							$picturePath === "images/large/blank.jpg" ||
							$picturePath === "images/med/blank.jpg" ||
							$picturePath === "images/thumb/blank.jpg"
						) {
							// Update value to 1 since the image(s) are set to blank.jpg
							$isBlank = 1;
							
						}
						
						/*	Setting image path based on session variable for new employee
							If a new employee is being created via json_gen.php, their data 
							is located in a temp folder. If view.php is being included in
							any other page, the session variable would be false, and the 
							image path would be the default location for live data.
						*/
						if ($reviewInProgress && $isBlank === 0) {
							$imagePath = "temp/" . $picturePath;
						} else {
							$imagePath = "../" . $picturePath;
						}
						
						
						if (file_exists($imagePath)) {
							echo "<img src='$imagePath' alt='$size'>";
						} else {
							echo "<p class='error'>Image not found: $imagePath</p>";
						}
					}

					echo '</div>'; // Close the container
				}

				// Close the border div
				echo '</div>';
			} else {
				echo "<p class='error'>Invalid JSON file: $fileToView</p>";
			}
		} else {
			echo "<p class='error'>File not found: $fileToView</p>";
		}
	}

?>
