<?php
// Start the session
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // User is not logged in; redirect to the login page or display an access denied message
    header("Location: ../data/login.php"); // Redirect to the login page
    exit; // Terminate script to ensure the redirect takes effect
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Employee Picture</title>
    <link rel="stylesheet" type="text/css" href="../css/admin.css">
    <style>
        /* Styling is kept as is */
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>	
    <script>
        $(document).ready(function () {
            $("#fileToUpdate").change(function () {
                var fileToView = $(this).val();
                if (fileToView) {
                    $.ajax({
                        type: "POST",
                        url: "view_pic.php", // Create a separate PHP file for viewing JSON data
                        data: { fileToView: fileToView },
                        success: function (data) {
                            $("#jsonView").html(data);
                        },
                    });
                } else {
                    $("#jsonView").html(''); // Clear the view if nothing is selected
                }
            });

            $("#setToBlank").change(function () {
                var setToBlank = this.checked;
                $("#newImageGroup").toggle(!setToBlank);
                $("#newImage").prop("disabled", setToBlank);
            });
        });
    </script>
</head>
<body>

    <h1 class="page-title">Change Employee Picture</h1>
    <div class="container large">
	    <center><button id="toggleButton" class="toggle-button">Toggle Instructions</button></center>
    <!-- Popup overlay and container -->
    <div class="overlay" id="popupOverlay">
        <div class="popup">         
            <div class="popup-content">
                <!-- Popup content goes here -->
                <p><b>INSTRUCTIONS:</b></p>
				<ol>
					<li>Select an employee from the dropdown list.</li>
					<li>Review the existing picture for the employee you wish to update.</li>
					<li>Determine if you are uploading a new photo or setting the user to "Blank."</li>
					<li>If you are setting the user photo to blank, follow these steps:
						<ol type="i">
							<li>Check the box for 'Set Photo to Blank' and note that the option to upload a new image disappears</li>
							<li>Click the 'Change Picture' button.</li>
							<li>The picture JSON data has been updated, and you will need to visit the 'Apply Updates' page.</li>
							<li>Follow the instructions on the 'Apply Updates' page to update the live site.</li>
						</ol>
					</li>
					<li>If you are changing the user photo, follow these steps:
						<ol type="i">
							<li>Click the 'Browse...' button under 'Upload New Image:'</li>
							<li>Select a picture that has a 1:1 ratio to ensure no image distortion during resizing.</li>
							<li>Click on 'Change Picture,' and the old picture will be overwritten with the new picture.</li>
						</ol>
					</li>
				</ol>

            </div>
			<button id="closeButton">Close</button>
        </div>
	    <script>
        // JavaScript to handle popup display and close
        document.getElementById('toggleButton').addEventListener('click', function() {
            var overlay = document.getElementById('popupOverlay');
            overlay.style.display = 'block';
        });

        document.getElementById('closeButton').addEventListener('click', function() {
            var overlay = document.getElementById('popupOverlay');
            overlay.style.display = 'none';
        });
    </script>	
    </div>
        <form method="post" action="" enctype="multipart/form-data">
            <label for="fileToUpdate">Select Employee Picture:</label>
            <select name="fileToUpdate" id="fileToUpdate">
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
			<div id="jsonView"></div>
            <div class="form-group">
                <div class="form-group" id="setToBlankGroup">
					<label for="setToBlank">Set Photo to Blank:</label>
					<input type="checkbox" name="setToBlank" id="setToBlank">
				</div>
            </div>

            <div class="form-group" id="newImageGroup">
                <label for="newImage">Upload New Image:</label>
                <input type="file" name="newImage" id="newImage" accept="image/*">
                <small>Accepted formats: JPEG or PNG</small>
            </div>
			
			<script>
				$(document).ready(function () {
					// Hide the checkbox, new image input, and setToBlank label initially
					$("#setToBlankGroup, #newImageGroup").hide();

					$("#fileToUpdate").change(function () {
						var selectedEmployee = $(this).val();
						if (selectedEmployee) {
							// If an employee is selected, show the checkbox, new image input, and setToBlank label
							$("#setToBlankGroup, #newImageGroup").show();
							$("#changePicButton").show();
						} else {
							// If no employee is selected, hide the checkbox, new image input, and setToBlank label
							$("#setToBlankGroup, #newImageGroup").hide();
							$("#changePicButton").hide();
						}
					});

					$("#setToBlank").change(function () {
						var setToBlank = this.checked;
						$("#newImage").prop("disabled", setToBlank);
					});
				});
			</script>
            <input type="submit" id="changePicButton" name="changePic" value="Change Picture" style="display: none;">

        </form>

    <?php
		// Include function for resizing PNG and JPG images
		include('functions/resizeImage.php');

		// Check if 'Set Photo to Blank' checkbox is selected
		$setToBlank = isset($_POST['setToBlank']) && $_POST['setToBlank'] === 'on';
		
		// If $setToBlank is true, delete the user image filesize
		if ($setToBlank) {
			// Check if a fileToUpdate is set
			if (isset($_POST['fileToUpdate'])) {
				$fileToUpdate = $_POST['fileToUpdate'];

				// Update the JSON data for the employee to reference 'blank.jpg'
				$employeeJSONFile = $fileToUpdate;
				$jsonContent = file_get_contents($employeeJSONFile);
				$jsonData = json_decode($jsonContent, true);

				if ($jsonData !== null) {
					// Update the 'picture' key to reference 'blank.jpg'
					$jsonData['picture'] = [
						"large" => "images/large/blank.jpg",
						"medium" => "images/med/blank.jpg",
						"thumbnail" => "images/thumb/blank.jpg"
					];

					// Convert to JSON format
					$json_string = json_encode($jsonData, JSON_PRETTY_PRINT);

					// Save the updated JSON data to the file
					if (file_put_contents($employeeJSONFile, $json_string) !== false) {
						// Delete the user's image files
						$uniqueID = pathinfo($fileToUpdate, PATHINFO_FILENAME);
						$largeImageFilename = "../images/large/$uniqueID.jpg";
						$medImageFilename = "../images/med/$uniqueID.jpg";
						$thumbImageFilename = "../images/thumb/$uniqueID.jpg";

						// Check if the image files exist and then delete them
						if (file_exists($largeImageFilename)) {
							unlink($largeImageFilename);
						}
						if (file_exists($medImageFilename)) {
							unlink($medImageFilename);
						}
						if (file_exists($thumbImageFilename)) {
							unlink($thumbImageFilename);
						}
					}
				} else {
					echo '<p id="updateStatus" class="failure">Invalid JSON file: $fileToUpdate</p>';
				}
			}
		}


		if (isset($_POST['changePic'])) {
			$fileToUpdate = $_POST['fileToUpdate']; // Existing JSON file
			$newImage = isset($_FILES['newImage']) ? $_FILES['newImage'] : null; // New image file
			$setToBlank = isset($_POST['setToBlank']) && $_POST['setToBlank'] === 'on';

			// Check if the "setToBlank" checkbox is selected
			if ($setToBlank) {
				// Update the JSON data for the employee to reference 'blank.jpg'
				$employeeJSONFile = $fileToUpdate;
				$jsonContent = file_get_contents($employeeJSONFile);
				$jsonData = json_decode($jsonContent, true);

				if ($jsonData !== null) {
					// Update the 'picture' key to reference 'blank.jpg'
					$jsonData['picture'] = [
						"large" => "images/large/blank.jpg",
						"medium" => "images/med/blank.jpg",
						"thumbnail" => "images/thumb/blank.jpg"
					];

					// Convert to JSON format
					$json_string = json_encode($jsonData, JSON_PRETTY_PRINT);

					// Save the updated JSON data to the file
					if (file_put_contents($employeeJSONFile, $json_string) !== false) {
						echo '<p id="updateStatus" class="success">Image set to blank successfully.</p>';
						
						//Update employees.json via update_data.php. This applies changes to live site.
						include 'update_data.php';
						// Add JavaScript code to redirect to admin.php after a delay
						echo '<script>setTimeout(function() { window.location.href = "admin.php"; }, 3000);</script>';
					} else {
						echo '<p id="updateStatus" class="failure">Failed to set the image to blank.</p>';
					}
				} else {
					echo '<p id="updateStatus" class="failure">Invalid JSON file: $fileToUpdate</p>';
				}
			} else {
			if (!empty($newImage['name'])) {
				$uniqueID = pathinfo($fileToUpdate, PATHINFO_FILENAME); // Extract unique ID from the selected file

				// Read the JSON data for the employee to get their unique ID
				$employeeJSONFile = $fileToUpdate;
				$jsonContent = file_get_contents($employeeJSONFile);
				$jsonData = json_decode($jsonContent, true);

				if ($jsonData !== null && isset($jsonData['uniqueID'])) {
					$uniqueID = $jsonData['uniqueID'];
					} else {
						echo '<p id="updateStatus" class="failure">Invalid or missing uniqueID in employee JSON data.</p>';
						// Handle the error appropriately
						exit; // Terminate the script if the uniqueID is not available
					}

				$existingImageFilename = "../images/large/$uniqueID.jpg"; // Assuming images are stored in 'images/large/'

				// Check if the uploaded file is an image
				$imageFileType = strtolower(pathinfo($newImage['name'], PATHINFO_EXTENSION));
				$allowedExtensions = array("jpg", "jpeg", "png"); // Add more if needed

				if (in_array($imageFileType, $allowedExtensions)) {
				// Update the filename for the uploaded image with the employee's unique ID
				$newImageFilename = "../images/large/$uniqueID.jpg"; // Update the filename to match the unique ID

				// Move the uploaded file to the specified location (overwrite the existing image)
				if (move_uploaded_file($newImage['tmp_name'], $newImageFilename)) {
				// Update the image filenames based on the unique ID
				$largeImageFilename = "../images/large/$uniqueID.jpg";
				$medImageFilename = "../images/med/$uniqueID.jpg";
				$thumbImageFilename = "../images/thumb/$uniqueID.jpg";

				// Resize the uploaded image to medium and thumbnail sizes
				resizeImage($newImageFilename, $largeImageFilename, 128, 128);
				resizeImage($newImageFilename, $medImageFilename, 89, 89);
				resizeImage($newImageFilename, $thumbImageFilename, 50, 50);

				// Update the employee's JSON data to reference the new image
				$jsonData['picture'] = [
					"large" => "images/large/$uniqueID.jpg",
					"medium" => "images/med/$uniqueID.jpg",
					"thumbnail" => "images/thumb/$uniqueID.jpg"
				];

				// Convert to JSON format
				$json_string = json_encode($jsonData, JSON_PRETTY_PRINT);

				// Save the updated JSON data to the file
				if (file_put_contents($employeeJSONFile, $json_string) !== false) {
					echo '<p id="updateStatus" class="success">Image changed and resized successfully.</p>';
					
					//Update employees.json via update_data.php. This applies changes to live site.
					include 'update_data.php';
					// Add JavaScript code to redirect to admin.php after a delay
					echo '<script>setTimeout(function() { window.location.href = "admin.php"; }, 3000);</script>';
				} else {
					echo '<p id="updateStatus" class="failure">Failed to update employee JSON data.</p>';
				}
			} else {
				echo '<p id="updateStatus" class="failure">Failed to move the uploaded image. Please try again.</p>';
			}
		} else {
			echo '<p id="updateStatus" class="failure">Invalid file format. Please upload an image in JPG, JPEG, or PNG format. Please try again.</p>';
		}
			} else {
						echo '<p id="updateStatus" class="failure">No new image file provided. Please try again.</p>';
					}
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
