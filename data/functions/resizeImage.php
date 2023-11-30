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
    
    // Function to resize an image
    function resizeImage($sourcePath, $destinationPath, $newWidth, $newHeight) {
        list($sourceWidth, $sourceHeight, $imageType) = getimagesize($sourcePath);

        if ($imageType == IMAGETYPE_JPEG) {
            $sourceImage = imagecreatefromjpeg($sourcePath);
        } elseif ($imageType == IMAGETYPE_PNG) {
            $sourceImage = imagecreatefrompng($sourcePath);
        } else {
            // Handle other image types or report an error
            return false;
        }

        $destinationImage = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresized($destinationImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);

        if ($imageType == IMAGETYPE_JPEG) {
            imagejpeg($destinationImage, $destinationPath, 90); // Adjust quality as needed
        } elseif ($imageType == IMAGETYPE_PNG) {
            imagepng($destinationImage, $destinationPath, 0); // Adjust quality as needed
        }

        imagedestroy($sourceImage);
        imagedestroy($destinationImage);

        return true;
    }
    ?>