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
	
	// Check if the "employee_review_in_progress" session variable is set
	if (isset($_SESSION['employee_review_in_progress']) && $_SESSION['employee_review_in_progress'] === true) {
		// Scan the "temp/json/" directory for JSON files
        $jsonDirectory = 'temp/json/';
        $jsonFiles = scandir($jsonDirectory, 1); // List files in descending order

        if (!empty($jsonFiles)) {
            // Find the first JSON file in the directory
            foreach ($jsonFiles as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) == 'json') {
                    $filename = $jsonDirectory . $file;
                    break;
                }
            }
		}
		// Redirect to the review.php page with the appropriate filename
		header("Location: review.php?filename=" . urlencode($filename));
		exit; // Terminate the script to ensure the redirect takes effect
	}
	
	// Check if the "Generate JSON" button is clicked
	if (isset($_POST['generate'])) {
		// Initialize a session variable to indicate that the user is starting the review process
		$_SESSION['employee_review_in_progress'] = true;
	}

	// Check if the session variable is not set or not true
	if (!isset($_SESSION['employee_review_in_progress']) || $_SESSION['employee_review_in_progress'] !== true) {
		// Clean up temporary files if they exist
		$tempJSONDirectory = 'temp/json/';
		$tempImagesDirectory = 'temp/images/';

		// Function to delete files in a directory
		function deleteFilesInDirectory($directory) {
			$files = glob($directory . '*');
			foreach ($files as $file) {
				if (is_file($file)) {
					unlink($file);
				}
			}
		}

		// Clean up temporary JSON files
		deleteFilesInDirectory($tempJSONDirectory);

		// Clean up temporary image files (large, med, thumb)
		$imageSizes = ['large/', 'med/', 'thumb/'];
		foreach ($imageSizes as $size) {
			deleteFilesInDirectory($tempImagesDirectory . $size);
		}

		// Unset the session variable
		unset($_SESSION['employee_review_in_progress']);
	}	
?>

<!DOCTYPE html>
<html>
<head>
    <title>JSON Data Generator</title>
    <link rel="stylesheet" type="text/css" href="../css/admin.css">
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1 class="page-title">JSON Data Generator</h1>
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
                        <li>Fill out each field in the form below.</li>
                        <li>Review the employee data to confirm your selection.</li>
                        <li>Click on the 'Generate JSON' button to create the employee data file.</li>
                        <li>You will be redirected the the 'Update Employees JSON' page. 
						<li>Follow the instructions on the 'Updates' page to apply changes to the live site.
                    </ol>
                </div>
                <center><button id="closeButton">Close</button></center>
            </div>
        </div>
        <form method="post" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Title:</label>
            <select name="title" id="title" required>
                <option value="">No Title</option>
            </select>
        </div>
        <script>
            $(document).ready(function () {
                // Fetch the data from config.json
                $.ajax({
                    url: 'conf/config.json',
                    dataType: 'json',
                    success: function (data) {
                        const titles = data.organization_Settings.titles;

                        // Get the select element
                        const select = $('#title');

                        // Extract and sort title names
                        const sortedTitles = Object.values(titles).sort();

                        // Populate the select with sorted title options
                        sortedTitles.forEach(function (title) {
                            select.append($('<option>', {
                                value: title,
                                text: title
                            }));
                        });
                    },
                    error: function () {
                        console.log('Error fetching config.json');
                    }
                });
            });
        </script>

            <div class="form-group">
                <label for="first">First Name:</label>
                <input type="text" name="first" id="first" required>
            </div>

            <div class="form-group">
                <label for="last">Last Name:</label>
                <input type="text" name="last" id="last" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" name="phone" id="phone" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" required>
            </div>

            <div class="form-group">
                <label for="cell">Cell:</label>
                <label class="radio-label" for="has_cell">
                    <input type="radio" name="cell_option" id="has_cell" value="yes" checked> Yes
                </label>
                <label class="radio-label" for="no_cell">
                    <input type="radio" name="cell_option" id="no_cell" value="no"> No
                </label>

                <div id="cell_input">
                    <small>If there is a mobile phone, enter it below</small>
                    <input type="text" name="cell" id="cell" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" placeholder="123-456-7890">
                </div>
            </div>

            <!-- JavaScript to show/hide the mobile phone input field based on radio button selection -->
            <script>
                const cellInput = document.getElementById('cell_input');
                const hasCellRadio = document.getElementById('has_cell');
                const noCellRadio = document.getElementById('no_cell');

                hasCellRadio.addEventListener('change', function () {
                    cellInput.style.display = this.checked ? 'block' : 'none';
                });

                noCellRadio.addEventListener('change', function () {
                    cellInput.style.display = this.checked ? 'none' : 'block';
                });
				
				cellInput.addEventListener("input", function (e) {
					const input = e.target;
					let value = input.value.replace(/\D/g, ''); // Remove non-digits
					
					// Restrict the input to a maximum of 10 digits
					value = value.slice(0, 10);
					
					if (value.length <= 10) {
						// Format the input as XXX-XXX-XXXX
						input.value = value
							.replace(/(\d{3})(\d{0,3})(\d{0,4})/, function(_, p1, p2, p3) {
								return p1 + (p2 ? '-' + p2 : '') + (p3 ? '-' + p3 : '');
							});
					}
				});
            </script>

            <div class="form-group">
                <label for="department">Department:</label>
                <select name="department" id="department" required>
                    <option value="" disabled selected>Select a Department</option>
                </select>
            </div>
            <script>
                $(document).ready(function () {
                    // Fetch the data from config.json
                    $.ajax({
                        url: 'conf/config.json',
                        dataType: 'json',
                        success: function (data) {
                            const departments = data.organization_Settings.Departments;

                            // Get the select element
                            const select = $('#department');

                            // Extract and sort department names
                            const sortedDepartments = Object.values(departments).sort();

                            // Populate the select with sorted department options
                            sortedDepartments.forEach(function (department) {
                                select.append($('<option>', {
                                    value: department,
                                    text: department
                                }));
                            });
                        },
                        error: function () {
                            console.log('Error fetching config.json');
                        }
                    });
                });
            </script>

            <div class="form-group">
                <label for="image">Image Upload:</label>
                <input type="file" name="image" id="image" accept="image/*">
                <small>Accepted formats: JPEG or PNG</small>
            </div>

            <div class="form-group">
                <center><input type="submit" name="generate" value="Generate JSON" margin-top="20 px" ></center>
            </div>
        </form>

        <?php
		include "functions/generate_unique_id.php"; // Include the script for generating a unique ID
		
        // Include function to resize an image using GD library
        include('functions/resizeImage.php');
            
		// Get Unique ID to be used for the filenames
		list($uniqueID, $collectedIDs) = generateUniqueID(); // Generate a unique ID

        if (isset($_POST['generate'])) {
            // Collect form data
            $title = $_POST['title'];
            $first = $_POST['first'];
            $last = $_POST['last'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $department = $_POST['department'];

            // Handle image upload
            $imageUploadDir = "temp/images/large/";
            $imageFilename = "blank.jpg"; // Default image filename

            // Check if an image was uploaded
            if (!empty($_FILES['image']['name'])) {
                $imageName = $_FILES['image']['name'];

                // Generate the JSON filename based on the first letter of the first name and last name
                $jsonFilename = "temp/" . $uniqueID . ".json";

                // Generate the image filename (excluding extension) based on the JSON filename
                $imageFilename = pathinfo($jsonFilename, PATHINFO_FILENAME) . '.jpg';

                // Ensure the upload directory exists (create if it doesn't)
                if (!file_exists($imageUploadDir)) {
                    mkdir($imageUploadDir, 0777, true);
                }

                //// Upload the image to the 'images/large/' directory with the generated filename
                $imagePath = $imageUploadDir . $imageFilename;

                // Check if the uploaded file is an image
                $imageFileType = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = array("jpg", "jpeg", "png"); // Add more if needed

                if (in_array($imageFileType, $allowedExtensions)) {
                    // Move the uploaded file to the specified location
                    move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);

                    // Resize the uploaded image to medium and thumbnail sizes
                    resizeImage($imagePath, "temp/images/large/" . $imageFilename, 128, 128);
                    resizeImage($imagePath, "temp/images/med/" . $imageFilename, 89, 89);
                    resizeImage($imagePath, "temp/images/thumb/" . $imageFilename, 50, 50);
                } else {
                    echo "Invalid file format. Please upload an image in JPG, JPEG, or PNG format.";
                }
            }

            // Set $cell based on the radio button value
            $cell_option = $_POST['cell_option'];
            if ($cell_option === "yes") {
                $cell = $_POST['cell'];
            } else {
                $cell = "No Mobile Phone";
            }

            // Create JSON structure
            $json_data = [
                "name" => [
                    "title" => $title,
                    "first" => $first,
                    "last" => $last
                ],
                "email" => $email,
                "phone" => $phone,
                "cell" => $cell,
                "picture" => [
                    "large" => "images/large/$imageFilename",
                    "medium" => "images/med/$imageFilename",
                    "thumbnail" => "images/thumb/$imageFilename"
                ],
                "department" => $department,
				"uniqueID" => $uniqueID
            ];

            // Generate a filename based on the first letter of the first name and last name
            $filename = "temp/json/" . $uniqueID . ".json";

            // Ensure the directory exists (create if it doesn't)
            $directory = dirname($filename);
            if (!file_exists($directory)) {
                if (!mkdir($directory, 0777, true)) {
                    die("Failed to create directory: $directory");
                }
            }

            // Convert to JSON format
            $json_string = json_encode($json_data, JSON_PRETTY_PRINT);

            // Save the JSON data to the specified file
            if (file_put_contents($filename, $json_string) === false) {
                die("Failed to save JSON data to file: $filename");
            }

            // Output success message
            echo "<h2>JSON Data Saved:</h2>";
            echo "<p>Data has been saved to: $filename";

            echo "<script>
                     setTimeout(function() {
						window.location.href = 'review.php?filename=" . urlencode($filename) . "'; // Redirect to review.php with filename parameter
					}, 1500); // Redirect after a 1.5 second delay (adjust the delay as needed)
                </script>";
        }      
    ?>
    </div>
	<script>
	// JavaScript to automatically format the phone input
	const phoneInput = document.getElementById("phone");

	phoneInput.addEventListener("input", function (e) {
		const input = e.target;
		let value = input.value.replace(/\D/g, ''); // Remove non-digits
		
		// Restrict the input to a maximum of 10 digits
		value = value.slice(0, 10);

		if (value.length <= 10) {
			// Format the input as XXX-XXX-XXXX
			input.value = value
				.replace(/(\d{3})(\d{0,3})(\d{0,4})/, function(_, p1, p2, p3) {
					return p1 + (p2 ? '-' + p2 : '') + (p3 ? '-' + p3 : '');
				});
		}
	});
	</script>
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