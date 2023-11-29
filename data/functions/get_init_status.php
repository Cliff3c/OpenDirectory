<?php
    // Check if a session is not already active before starting one
    if (session_status() === PHP_SESSION_NONE) {
        session_start(); // Start the session if it's not already started
    }
    // Function to read the config JSON file and fetch the initialization status
    function getInitializationStatus() {
        $configFile = '../data/conf/config.json';

        if (file_exists($configFile)) {
            $configData = json_decode(file_get_contents($configFile), true);

            // Check if 'initialize_status' key exists and if the value is 0 or 1
            if (isset($configData['initialize_status']) && ($configData['initialize_status'] === "0" || $configData['initialize_status'] === "1")) {
                $status = intval($configData['initialize_status']);
                
                // Redirect to initialize.php if status is 0
                if ($status === 0) {
                    $_SESSION['initializeToken'] = 1;
                    header("Location: initialize.php");
                    exit();
                }
                
                // Return the status (1) if it's 1, no actions needed
                return $status;
            } else {
                // Display error message if status is not 0 or 1 (corrupt install)
                echo "Installation is corrupt. Please reinstall the system.";
                exit();
            }
        } else {
            // Config file doesn't exist or unable to load; display error message
            echo "Config file not found. Please reinstall the system.";
            exit();
        }
    }
?>
