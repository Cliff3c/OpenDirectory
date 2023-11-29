<?php
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
	
	function generateUniqueID($length = 7) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$id = '';

		$collectedIDs = getCollectedIDs(); // Get collected unique IDs

		do {
			$id = '';
			for ($i = 0; $i < $length; $i++) {
				$id .= $characters[rand(0, strlen($characters) - 1)];
			}
		} while (in_array($id, $collectedIDs)); // Check if the ID already exists in the collected IDs

		return [$id, $collectedIDs]; // Return both the generated ID and collected IDs
	}

	function getCollectedIDs() {
		$dataDirectory = '../../data/employees/'; // Adjust the path as needed

		$collectedIDs = [];

		// Get a list of JSON files in the directory
		$jsonFiles = glob($dataDirectory . '*.json');

		// Loop through JSON files and collect unique IDs
		foreach ($jsonFiles as $jsonFile) {
			$jsonData = json_decode(file_get_contents($jsonFile), true);
			if ($jsonData !== null && isset($jsonData['uniqueID'])) {
				$collectedIDs[] = $jsonData['uniqueID'];
			}
		}

		return $collectedIDs;
	}

	// Generate a unique alphanumeric ID
	list($uniqueID, $collectedIDs) = generateUniqueID();

	// Output the unique ID - remove or comment out after testing
	//echo 'Generated Unique ID: ' . $uniqueID . '<br>';

	// Output collected unique IDs - remove or comment out after testing
	//echo 'Collected Unique IDs: ' . implode(', ', $collectedIDs);
?>
