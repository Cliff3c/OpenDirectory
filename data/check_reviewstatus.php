<?php
   // Check if the user is in the middle of creating an employee review
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

            if (isset($filename)) {
                // Display a warning message and a link to continue the review
                echo '<div class="warning-message">';
                echo '<center>You are in the middle of creating an employee. ';
				echo '<a class="buttonWarning" href="review.php?filename=' . urlencode($filename) . '">Continue</a></center>';
                echo '</div>';
            } else {
                // Display a different message when no JSON files are found
                echo '<div class="no-json-file-message">No JSON files found in the directory.</div>';
            }
        } else {
            // Display a different message when the directory is empty
            echo '<div class="no-json-file-message">The directory is empty.</div>';
        }
    } else {
        // Display a different message or nothing when the review is not in progress
        //echo '<div class="no-review-message">No employee review in progress.</div>';
    }
?>