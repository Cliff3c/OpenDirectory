<?php
if (isset($_POST['cancel'])) {
    // Get the filename to be canceled from the review process
    $filenameToCancel = isset($_POST['filenameToCancel']) ? $_POST['filenameToCancel'] : '';

    if (!empty($filenameToCancel)) {
        // Specify the directory where the JSON files are stored
        $directory = '';

        // Construct the file path for the JSON file
        $jsonFilePath = $directory . $filenameToCancel;

        if (file_exists($jsonFilePath)) {
            // Load the JSON data from the selected file
            $jsonContent = file_get_contents($jsonFilePath);
            $jsonData = json_decode($jsonContent, true);

            if ($jsonData !== null) {
                // Extract image file paths from the JSON data
                if (isset($jsonData['picture']) && is_array($jsonData['picture'])) {
                    $picturePaths = '';
                    foreach ($jsonData['picture'] as $size => $picturePath) {
                        // Normalize the path to use forward slashes
                        $normalizedPath = str_replace("\\", "/", $picturePath);

                        // Append the path to the $picturePaths string
                        $picturePaths .= "temp/" . $normalizedPath . ', ';

                        // Delete image file if it exists
                        $imagePath = "temp/" . $normalizedPath;						
                        if (file_exists($imagePath)) {
                            unlink($imagePath);
							echo "<p class='success'>File '$imagePath' deleted successfully.</p>";
                        }
                    }
                    // Remove the trailing comma and space
                    $picturePaths = rtrim($picturePaths, ', ');
                }

                // Delete the JSON file
                if (unlink($jsonFilePath)) {
                    echo "<p class='success'>File '$filenameToCancel' deleted successfully.</p>";
                    echo "<script>
                        setTimeout(function() {
                            window.location.href = 'admin.php'; // Redirect to admin menu page
                        }, 10000); // Redirect after a 10-second delay (adjust the delay as needed)
                    </script>";
                } else {
                    echo "<p class 'error'>Failed to delete file '$filenameToCancel'.</p>";
                }
            } else {
                echo "<p class='error'>Failed to decode JSON data from the selected file.</p>";
            }
        } else {
            echo "<p class='error'>File '$filenameToCancel' does not exist.</p>";
        }
    } else {
        echo "<p class='error'>Filename not provided for cancellation.</p>";
    }
}
?>
