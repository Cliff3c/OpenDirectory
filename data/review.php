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

// Get the filename from the URL
$filename = isset($_GET['filename']) ? $_GET['filename'] : '';

if (empty($filename)) {
    echo "Filename not provided in the URL.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Review New Employee</title>
    <link rel="stylesheet" type="text/css" href="../css/admin.css">
    <style>
        /* Styling is kept as is */
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
		$(document).ready(function () {
			// Function to load JSON data for the selected file
			function loadJSONData(fileToView) {
				if (fileToView) {
					// Create a variable to pass to view.php
					var reviewInProgress = <?php echo isset($_SESSION['employee_review_in_progress']) ? 'true' : 'false'; ?>;
					
					$.ajax({
						type: "POST",
						url: "view.php",
						data: { fileToView: fileToView, reviewInProgress: reviewInProgress }, // Include the variable
						success: function (data) {
							$("#jsonView").html(data);
						},
					});
				}
			}

			// Initial load of data
			loadJSONData("<?php echo $filename; ?>");

			// Click event for the "Apply" button
			$("#applyButton").click(function() {
				var filenameToMove = "<?php echo $filename; ?>"; // Get the filename from PHP

				$.ajax({
					url: 'move_files.php',
					type: 'POST', // Use POST to send the filename
					data: { moveFiles: true, filenameToMove: filenameToMove },
					success: function(moveResponse) {
						console.log("Response from move_files.php:", moveResponse);
						// After successfully moving files, proceed to update_data.php
						$.ajax({
							url: 'update_data.php',
							type: 'GET',
							success: function(response) {
								console.log("Response from update_data.php:", response);
								$("#updateStatus").text(response);
								$("#successMessage").text("Employee added successfully to the live site.");
								$("#successMessage").show();

								// After other actions, unset the session variable
								$.ajax({
									url: 'unset_review.php',
									type: 'GET',
									success: function(unsetResponse) {
										console.log("Response from unset_review_in_progress.php:", unsetResponse);
									},
									error: function() {
										console.log("Error unsetting the review in progress.");
									}
								});

								// Delay the redirection by 1.5 seconds
								setTimeout(function() {
									window.location.href = 'admin.php'; // Redirect to the admin page
								}, 1500); // 1500 milliseconds (1.5 seconds)
							},
							error: function() {
								console.log("Error updating JSON file.");
								$("#updateStatus").text("Error updating JSON file.");
							}
						});
					},
					error: function() {
						console.log("Error moving files.");
					}
				});
			});


			// Click event for the "Cancel" button
			$("#cancelButton").click(function() {
				var filenameToCancel = "<?php echo $filename; ?>"; // Get the filename from PHP
				$.ajax({
					url: 'cancel.php',
					type: 'POST',
					data: { cancel: true, filenameToCancel: filenameToCancel },
					success: function(response) {
						$("#successMessage").html(response);
					
						// After other actions, unset the session variable
						$.ajax({
							url: 'unset_review.php',
							type: 'GET',
							success: function(unsetResponse) {
								console.log("Response from unset_review_in_progress.php:", unsetResponse);
							},
							error: function() {
								console.log("Error unsetting the review in progress.");
							}
						});
						
						// Delay the redirection by 1.5 seconds
						setTimeout(function() {
							window.location.href = 'admin.php'; // Redirect to the admin page
						}, 1500); // 1500 milliseconds (1.5 seconds)
					},
					error: function() {
						$("#successMessage").text("Error canceling the review process.");
					}
				});
			});
		});
		</script>
</head>
<body>
    <h1 class="page-title">Review New Employee</h1>
    <div class="container large">
        <!-- Output JSON data for the selected file -->
        <center>
            <button id="toggleButton" class="toggle-button">Toggle Instructions</button>
		</center>
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
                    <div class="json-data-explanation">
                        <p><b>Sample JSON Data:</b></p>
                            <code style="display: block; background-color: #f4f4f4; padding: 5px; font-size: 11px; line-height: 0.5; text-align: left; white-space: pre;">                        
                            <br>{
                                <br>"name": {
                                <br>    "title": "Mr.",
                                <br>    "first": "John",
                                <br>    "last": "Doe"
                                <br>},
                                <br>"email": "jdoe@MYDOMAIN.COM",
                                <br>"phone": "555-123-1234",
                                <br>"cell": "No Mobile Phone",
                                <br>"picture": {
                                <br>    "large": "images/large/gEwcORE.jpg",
                                <br>    "medium": "images/med/gEwcORE.jpg",
                                <br>    "thumbnail": "images/thumb/gEwcORE.jpg"
                                <br>},
                                <br>"department": "DEPARTMENT",
                                <br>"uniqueID": "gEwcORE"
                            <br>}
                        </code>                        
                        
                        <p><b>Explanation:</b></p>
                        <i>The employee's information is logged in JSON format. By reviewing the sample code, you can understand the JSON formatting and values for each defined item as laid out below:</i>
                        <ul>
                            <li><b>Name:</b> The employee's name with title, first name, and last name.</li>
                            <li><b>Email:</b> The employee's email address.</li>
                            <li><b>Phone:</b> The employee's primary phone number.</li>
                            <li><b>Cell:</b> Whether the employee has a mobile phone.</li>
                            <li><b>Picture:</b> Links to the employee's profile pictures in various sizes.</li>
                            <li><b>Department:</b> The department or team the employee belongs to.</li>
                            <li><b>Unique ID:</b> A unique identifier for the employee's data.</li>
                        </ul>
                        <br>
                        <i>If the data looks accurate, click </i><b><span style="color: green;">Apply</span></b><i>, and the employee will be created and added to the live site. Otherwise, click </i><b><span style="color: red;">Cancel</span></b><i>, and the data will be discarded.</i><br>
                    </div>
                </div>
                <center>
                    <button id="closeButton">Close</button>
                </center>
            </div>
        </div>

        <div class="container-left" style="text-align: left;">
            <div id="jsonView"></div>
        </div>
		
		<!-- Action buttons to carry out tasks -->
		<div class="button-container">
			<div class="centered-buttons">
				<button id="applyButton">Apply</button>
				<button id="cancelButton" class="cancel-button">Cancel</button>
			</div>
		</div>
		
		<div id="successMessage" class="success"></div>
    </div>
    <div class="footer">
        <div class="logout-button">
            <a href="../data/admin.php" class="button">Back to Admin</a>
        </div>
        <div class="user-info">
            Username: <?php echo $_SESSION['username']; ?>
        </div>
		
	<!-- PUT HERE FOR DEBUGGING
		<?php
		// Echo the value of $_SESSION['employee_review_in_progress']
		if (isset($_SESSION['employee_review_in_progress'])) {
			echo "Employee review in progress: " . ($_SESSION['employee_review_in_progress'] ? 'Yes' : 'No');
		} else {
			echo "Employee review in progress: Not set";
		}
		?> 
	-->
    </div>
</body>
</html>
