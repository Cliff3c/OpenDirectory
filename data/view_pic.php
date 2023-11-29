<?php
	if (isset($_POST['fileToView'])) {
		$fileToView = $_POST['fileToView'];

		if (file_exists($fileToView)) {
			$jsonContent = file_get_contents($fileToView);
			$jsonData = json_decode($jsonContent, true);

			if ($jsonData !== null) {
				// Wrap JSON data and images in div elements with border class
				echo '<div class="border-box">';

				// Display associated images if they exist
				if (isset($jsonData['picture']) && is_array($jsonData['picture'])) {
					echo '<label2>IMAGES:</label2>';
					echo '<div class="image-container">'; // Add the container

					foreach ($jsonData['picture'] as $size => $picturePath) {
						if ($picturePath === "images/blank.jpg") {
							// Skip displaying blank.jpg
							continue;
						}

						$imagePath = "../" . $picturePath;
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
