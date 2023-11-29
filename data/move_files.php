<?php
if (isset($_POST['moveFiles'])) {
    // Get the full file path to be moved from the temporary folder
    $filePathToMove = isset($_POST['filenameToMove']) ? $_POST['filenameToMove'] : '';
	// Get the filename without extension
	$filenameWithoutExtension = pathinfo(basename($filePathToMove), PATHINFO_FILENAME);
	
	echo "The path is '$filePathToMove'.";
    if (!empty($filePathToMove)) {
        // Define source and destination paths for JSON and images
        $sourceJSONDirectory = 'temp/json/';
        $destinationJSONDirectory = 'employees/';
        
        $sourceImagesDirectory = 'temp/images/';
        $destinationImagesDirectory = '../images/';

        // Extract the filename from the full path
        $filenameToMove = basename($filePathToMove);

        // Construct the source and destination file paths for JSON and images
        $sourceJSONFilePath = $sourceJSONDirectory . $filenameToMove;
        $destinationJSONFilePath = $destinationJSONDirectory . $filenameToMove;

        // Move the JSON file
        if (file_exists($sourceJSONFilePath) && is_readable($sourceJSONFilePath)) {
            if (rename($sourceJSONFilePath, $destinationJSONFilePath)) {
                echo "JSON file '$filenameToMove' moved successfully.";
            } else {
                echo "Failed to move JSON file '$filenameToMove'.";
            }
        } else {
            echo "JSON file '$filenameToMove' does not exist or is not readable in the temporary folder.";
        }
		// Add debug output for file paths
		echo "Source JSON Path: $sourceJSONFilePath<br>";
		echo "Destination JSON Path: $destinationJSONFilePath<br>";
		
        // Move associated images (large, med, thumb)
        $imageSizes = ['large', 'med', 'thumb'];
        foreach ($imageSizes as $size) {
			// Construct the source and destination paths for each image size
			$sourceImagesPath = $sourceImagesDirectory . $size . '/' . $filenameWithoutExtension . '.jpg';
			$destinationImagesPath = $destinationImagesDirectory . $size . '/' . $filenameWithoutExtension . '.jpg';

			// Move the image file (example for large size)
			if (file_exists($sourceImagesPath) && is_readable($sourceImagesPath)) {
				if (rename($sourceImagesPath, $destinationImagesPath)) {
					echo "Image file '$filenameWithoutExtension.jpg' ($size) moved successfully.";
				} else {
					echo "Failed to move image file '$filenameWithoutExtension.jpg' ($size).";
				}
			} else {
				echo "Image file '$filenameWithoutExtension.jpg' ($size) does not exist or is not readable in the temporary folder.";
			}
		}
    } else {
        echo "File path not provided for moving.";
    }
}
?>
