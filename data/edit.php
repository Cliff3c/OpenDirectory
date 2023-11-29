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
    <title>Edit JSON Data</title>
    <link rel="stylesheet" type="text/css" href="../css/admin.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1 class="page-title">Edit JSON Data</h1>
    <div class="container">
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
                        <li>Review the populated fields to identify necessary changes.</li>
                        <li>For the 'Cell' field:
                            <ol type="i">
                                <li>Check the 'Cell' box and choose 'Has Cell' or 'No Cell.'
                                <li>If updating the employee's cell number, modify the text field below.
                                <li>Selecting 'No Cell' sets the default value to 'No Mobile Phone.'
                            </ol>
                        </li>
                        <li>Enable the edit option by checking the appropriate field(s).
                        <li>After making all necessary changes, click 'Save JSON.'
                        <li>Follow the instructions on the 'Apply Updates' page to apply changes to the live site.
                    </ol>
                </div>
                <center><button id="closeButton">Close</button></center>
            </div>
        </div>

        <!-- Form for selecting a JSON file to edit -->
        <form method="post" action="">
            <label for="jsonFile">Select JSON File:</label>
            <select name="jsonFile" id="jsonFile" required onchange="document.getElementById('loadJsonButton').click();">
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

            <input type="submit" name="load" value="Load JSON" id="loadJsonButton" style="display: none;">
        </form>

        <?php
			function createFormFields($data, $post) {
				foreach ($data as $key => $value) {
					if ($key === 'name') {
						echo '<div class="form-group">';
						createCheckbox('title', $value['title'], $post);
						echo '</div>';
						echo '<div class="form-group">';
						createCheckbox('first', $value['first'], $post);
						createCheckbox('last', $value['last'], $post);
						echo '</div>';
					} elseif ($key !== 'picture' && $key !== 'uniqueID') {
						echo '<div class="form-group">';
						createCheckbox($key, $value, $post);
						if ($key === 'cell') {
							// Create a "Cell" field with radio buttons
							echo '<div class="form-group">';
							echo '<input type="radio" name="cell_option" id="has_cell" value="has_cell" checked> Has Cell';
							echo '<input type="radio" name="cell_option" id="no_cell" value="no_cell"> No Cell';
							echo '</div>';
							
							$cellValue = isset($decodedData['cell']) ? htmlspecialchars($decodedData['cell']) : '';
							
							echo '<div class="form-group">';
							echo '<input type="text" name="cell" id="cell" value="' . $cellValue . '" required>';
							echo '</div>';
							
							echo '<script>
								const cellInput = document.getElementById("cell");

								cellInput.addEventListener("input", function (e) {
									const input = e.target;
									let value = input.value.replace(/\\D/g, ""); // Remove non-digits

									// Restrict the input to a maximum of 10 digits
									value = value.slice(0, 10);

									if (value.length <= 10) {
										// Format the input as XXX-XXX-XXXX
										input.value = value.replace(/(\\d{3})(\\d{0,3})(\\d{0,4})/, function(_, p1, p2, p3) {
											return p1 + (p2 ? "-" + p2 : "") + (p3 ? "-" + p3 : "");
										});
									}
								});
							</script>';
						} elseif ($key === 'phone') {
							// Create a "Phone" field as an input type "text" with the correct ID
							createTextField($key, $value, $post);
						}
						echo '</div>';
					}
				}
			}

        function createCheckbox($fieldName, $fieldValue, $post) {
            echo '<input type="checkbox" name="enable_' . $fieldName . '" class="enable-checkbox">';
            echo '<label for="' . $fieldName . '">' . ucfirst($fieldName) . ':</label>';
            if ($fieldName === 'department' || $fieldName === 'title') {
                echo createDropdown($fieldName, $fieldValue, $post);
            } elseif ($fieldName !== 'phone' && $fieldName !== 'cell') {
                echo createTextField($fieldName, $fieldValue, $post);
            }
        }

		function createTextField($fieldName, $fieldValue, $post) {
			echo '<input type="text" name="' . $fieldName . '" id="' . $fieldName . '" value="' . htmlspecialchars($fieldValue) . '" required';
			echo ' ' . (isset($post['enable_' . $fieldName]) && $post['enable_' . $fieldName] == 'on' ? '' : 'disabled');
			echo '><br>';
		}

		function createDropdown($fieldName, $fieldValue, $post) {
			echo '<select name="' . $fieldName . '" id="' . $fieldName . '" required';
			echo ' ' . (isset($post['enable_' . $fieldName]) && $post['enable_' . $fieldName] == 'on' ? '' : 'disabled');
			echo '>';
			
			$configFile = file_get_contents('conf/config.json');
			$configData = json_decode($configFile, true);
			$options = [];

			if ($fieldName === 'department' && isset($configData['organization_Settings']['Departments'])) {
				$options = $configData['organization_Settings']['Departments'];
			} elseif ($fieldName === 'title' && isset($configData['organization_Settings']['titles'])) {
				$options = $configData['organization_Settings']['titles'];
			}

			foreach ($options as $option) {
				$selected = ($option === $fieldValue) ? 'selected' : '';
				echo '<option value="' . htmlspecialchars($option) . '" ' . $selected . '>' . htmlspecialchars($option) . '</option>';
			}
			echo '</select>';
		}

		if (isset($_POST['load'])) {
			$selectedFile = $_POST['jsonFile'];
			$jsonFilePath = $selectedFile;
			$jsonData = file_get_contents($jsonFilePath);
			$decodedData = json_decode($jsonData, true);

			if ($decodedData !== null) {
				echo '<form method="post" action="">';
				echo '<input type="hidden" name="jsonFile" value="' . htmlspecialchars($selectedFile) . '">';
				createFormFields($decodedData, $_POST);
				echo '<input type="submit" name="submit_button" value="Save JSON">';
				echo '</form>';
			} else {
				echo "Failed to decode JSON data from the selected file.";
			}
		}

		// JavaScript to enable/disable input fields based on checkbox state
		echo '<script>
			const checkboxes = document.querySelectorAll(".enable-checkbox");
			checkboxes.forEach(checkbox => {
				const inputField = document.getElementById(checkbox.name.replace("enable_", ""));
				checkbox.addEventListener("change", () => {
					inputField.disabled = !checkbox.checked;
				});
			});
		</script>';
		
		// Javascript to format the 'Phone' field for a 10 digit phone number (###-###-####)
		echo '<script>
			// JavaScript to automatically format the phone input
			document.addEventListener("DOMContentLoaded", function () {
				const phoneInput = document.getElementById("phone");

				phoneInput.addEventListener("input", function (e) {
					const input = e.target;
					let value = input.value.replace(/\D/g, \'\'); // Remove non-digits

					// Restrict the input to a maximum of 10 digits
					value = value.slice(0, 10);

					if (value.length <= 10) {
						// Format the input as XXX-XXX-XXXX
						input.value = value.replace(/(\d{3})(\d{0,3})(\d{0,4})/, function(_, p1, p2, p3) {
							return p1 + (p2 ? \'-\' + p2 : \'\') + (p3 ? \'-\' + p3 : \'\');
						});
					}
				});
			});
		</script>';

		// JavaScript to control the "Cell" field behavior
		echo '<script>
			// JavaScript to control the "Cell" field behavior
			document.addEventListener("DOMContentLoaded", function () {
				const enableCellCheckbox = document.querySelector("[name=\'enable_cell\']");
				const hasCellRadio = document.getElementById("has_cell");
				const noCellRadio = document.getElementById("no_cell");
				const cellInput = document.getElementById("cell");
				const originalCellData = ' . json_encode(isset($decodedData['cell']) ? $decodedData['cell'] : '') . ';

				// Initial state: Disable radio buttons and cell field
				cellInput.value = originalCellData;
				hasCellRadio.disabled = true;
				noCellRadio.disabled = true;
				cellInput.disabled = true;

				enableCellCheckbox.addEventListener("change", function () {
					if (enableCellCheckbox.checked) {
						// Enable radio buttons and cell field
						hasCellRadio.disabled = false;
						noCellRadio.disabled = false;
						cellInput.disabled = false;
						if (hasCellRadio.checked) {
							// Set the value to the original cell data when "Has Cell" is selected
							cellInput.value = originalCellData;
						}
					} else {
						// Disable radio buttons and cell field
						hasCellRadio.disabled = true;
						noCellRadio.disabled = true;
						cellInput.disabled = true;

						if (noCellRadio.checked) {
							// Set the value to "No Mobile Phone" when "No Cell" is selected
							cellInput.value = "No Mobile Phone";
						}
					}
				});

				hasCellRadio.addEventListener("change", function () {
					if (hasCellRadio.checked && enableCellCheckbox.checked) {
						// Enable the cell field and format it as needed
						cellInput.disabled = false;
						if (originalCellData !== "No Mobile Phone") {
							// Set the value to the original cell data if it is not "No Mobile Phone"
							cellInput.value = originalCellData;
						}
					}
				});

				noCellRadio.addEventListener("change", function () {
					if (noCellRadio.checked) {
						// Disable the cell field and set the value to "No Mobile Phone"
						cellInput.disabled = true;
						cellInput.value = "No Mobile Phone";
					}
				});
			});
		</script>';

		if (isset($_POST['submit_button']) && $_POST['submit_button'] === 'Save JSON') {
			$selectedFile = $_POST['jsonFile'];
			$jsonFilePath = $selectedFile;
			$jsonData = file_get_contents($jsonFilePath);
			$decodedData = json_decode($jsonData, true);

			if ($decodedData !== null) {
				foreach ($_POST as $key => $value) {
					if ($key !== 'jsonFile' && $key !== 'submit_button' && strpos($key, 'enable_') !== 0 && $key !== 'cell_option') {
						if ($key === 'title' || $key === 'first' || $key === 'last') {
							if (!isset($decodedData['name'])) {
								$decodedData['name'] = [];
							}
							$decodedData['name'][$key] = $value;
						} elseif ($key === 'cell') {
							// Update the "Cell" field in the JSON data
							$decodedData['cell'] = $value;
						} else {
							$decodedData[$key] = $value;
						}
					}
				}
				
				if (isset($_POST['cell_option'])) {
					if ($_POST['cell_option'] === 'no_cell') {
						// "No Cell" is selected, update the "Cell" field to "No Mobile Phone"
						$decodedData['cell'] = "No Mobile Phone";
					}
				}
				

				$jsonString = json_encode($decodedData, JSON_PRETTY_PRINT);
				
				if (file_put_contents($jsonFilePath, $jsonString) !== false) {
					include 'update_data.php';
					echo "<script>
						setTimeout(function() {
							window.location.href = 'admin.php'; // Redirect to menu page at 'admin.php'
						}, 2000);
					</script>";
				} else {
					echo "Failed to save JSON data to the selected file.";
				}
				} else {
					echo "Failed to decode JSON data from the selected file.";
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
