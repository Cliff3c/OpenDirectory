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
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Backup Page</title>
    <link rel="stylesheet" type="text/css" href="../css/admin.css">
    <link rel="stylesheet" type="text/css" href="../css/settings.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<h1 class="page-title">Backup & Restore Data</h1>
    <div class="container small">
        <!-- Backup and Restore Form -->
        <form method="post" action="" enctype="multipart/form-data">
            <button id="toggleBackup" class="modal-buttons" type="button">Backup Site Data</button>
            <button id="toggleRestore" class="modal-buttons" type="button">Restore Site Data</button>
            <!-- Popup overlay and container for instructions -->
            <div class="overlay" id="backupPopupOverlay">
                <div class="popup">
                    <div class="popup-content">
                        <style>                                
                            ol {
                                text-align: left;
                            }
                            li {
                                text-align: left;
                            }
                        </style>
                        <p><b>INSTRUCTIONS FOR BACKUP:</b></p>
                        <ol>
                            <li>Click on 'Backup Site Data.'</li>
                            <li>A ZIP archive file will be downloaded.</li>
                            <li>Save the file in a safe place.</li>                                    
                        </ol>
                    </div>                       
                    <input type="submit" class="modal-buttons" name="backup" value="Backup Site Data">
                    <button class="button2" id="closeBackupButton">Close</button>
                </div>
                <?php
                    // Check if backup button is clicked
                    if (isset($_POST['backup'])) {
                        $backupFile = createBackup();
                        if ($backupFile) {
                            // Set headers for file download
                            header('Content-Type: application/octet-stream');
                            header('Content-Disposition: attachment; filename="' . basename($backupFile) . '"');
                            header('Content-Length: ' . filesize($backupFile));
                            ob_clean();
                            flush();
                            readfile($backupFile);

                            // Delete contents of the backups folder after download
                            $backupDir = 'backups/';
                            $files = glob($backupDir . '*'); // Get all files in the directory
                            foreach ($files as $file) {
                                if (is_file($file)) {
                                    unlink($file); // Delete the file
                                }
                            }

                            exit;
                        } else {
                            echo 'Backup creation failed.';
                        }
                    }
                ?>
            </div>

            <!-- Popup overlay and container for restore instructions -->
            <div class="overlay" id="restorePopupOverlay">
                <div class="popup">
                    <div class="popup-content">
                        <style>                                
                            ol {
                                text-align: left;
                            }
                            li {
                                text-align: left;
                            }
                        </style>
                        <p><b>INSTRUCTIONS FOR RESTORE:</b></p>
                        <ol>
                            <li>Click on 'Restore Site Data.'</li>
                            <li>Select your zipped backup file.</li>
                            <li>Click on the 'Restore' button.</li>
                            <li>Wait for your data to restore.</li>
                            <li><i>Refer to <a href="help.php"><b>HELP</b></a> for sample data.</i></li>
                        </ol>
                    </div>                       
                    <input type="file" name="uploadedFile" accept=".zip">
                    <input type="submit" name="restore" value="Restore Site Data">
                    <button class="button2" id="closeRestoreButton">Close</button>
                </div>
            </div>
            <a class="button" href="../data/reset.php">Factory Reset</a>
        </form>
        <?php                       
            include('functions/resizeImage.php');
            
            function createBackup() {
                $backupDir = 'backups/';
                $timestamp = date('Y-m-d_H-i-s');
                $zipFileName = $backupDir . 'backup_' . $timestamp . '.zip';

                $filesToBackup = [
                    'conf/config.json',
                    'users/users.json'
                ];

                $imageDirectory = '../images/large/';
                $imageFiles = scandir($imageDirectory);
                foreach ($imageFiles as $file) {
                    $filePath = $imageDirectory . $file;
                    if (is_file($filePath)) {
                        $filesToBackup[] = $filePath;
                    }
                }

                // Add all files in /data/employees/ directory
                $employeeFiles = glob('employees/*');
                $filesToBackup = array_merge($filesToBackup, $employeeFiles);

                // Add all files in /images/large/ directory
                $imageFiles = glob('images/large/*');
                $filesToBackup = array_merge($filesToBackup, $imageFiles);

                // Create a zip archive
                $zip = new ZipArchive();
                if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                    foreach ($filesToBackup as $file) {
                        if (is_file($file)) {
                            $filePathInZip = str_replace('../', '', $file);
                            $zip->addFile($file, $filePathInZip);
                        }
                    }
                    $zip->close();

                    // Return the path to the created zip file
                    return $zipFileName;
                } else {
                    return false; // Failed to create the zip file
                }
            }

            function deleteDirectory($path) {
                if (!file_exists($path)) {
                    return true;
                }
            
                if (!is_dir($path)) {
                    return unlink($path);
                }
            
                foreach (scandir($path) as $item) {
                    if ($item == '.' || $item == '..') {
                        continue;
                    }
            
                    if (!deleteDirectory($path . DIRECTORY_SEPARATOR . $item)) {
                        return false;
                    }
                }
            
                return rmdir($path);
            }

            if (isset($_POST['restore'])) {
                if ($_FILES['uploadedFile']['error'] === UPLOAD_ERR_OK) {
                    $tempFile = $_FILES['uploadedFile']['tmp_name'];
                    $extractPath = 'temp_restore/';
            
                    // Check if the directory exists or create it
                    if (!file_exists($extractPath) || !is_dir($extractPath)) {
                        mkdir($extractPath, 0777, true); // Creates the directory recursively
                    } else {
                        // Clear existing files and folders within temp_restore
                        deleteDirectory($extractPath);
                        mkdir($extractPath, 0777, true); // Recreate the directory
                    }
            
                    $zip = new ZipArchive();
            
                    if ($zip->open($tempFile) === true) {
                        $zip->extractTo($extractPath);
                        $zip->close();
            
                        // Validate extracted contents
                        $requiredFolders = ['conf', 'employees', 'images', 'images/large', 'users'];
                        $requiredFiles = ['conf/config.json', 'users/users.json'];
            
                        $isValid = true;
            
                        foreach ($requiredFolders as $folder) {
                            if (!is_dir($extractPath . $folder)) {
                                $isValid = false;
                                break;
                            }
                        }
            
                        foreach ($requiredFiles as $file) {
                            if (!file_exists($extractPath . $file)) {
                                $isValid = false;
                                break;
                            }
                        }
            
                        $employeeFiles = glob($extractPath . 'employees/*.json');
                        $imageFiles = glob($extractPath . 'images/large/*.jpg');
            
                        if (count($employeeFiles) === 0 || count($imageFiles) !== count(glob($extractPath . 'images/large/*.jpg'))) {
                            $isValid = false;
                        }
            
                        // If all validations pass, proceed with the restore process
                        if ($isValid) {
                            // Performing restore operations here
                            //Step 1: Clear out any data that may currently exist:
                            $directoriesToClear = ['conf/', 'employees/', '../images/large/', '../images/med/', '../images/thumb/', 'users/'];

                            foreach ($directoriesToClear as $directory) {
                                $files = glob($directory . '*'); // Get all files within the directory
                                foreach ($files as $file) {
                                    if (is_file($file)) {
                                        unlink($file); // Delete the file
                                    }
                                }
                            }

                            //Step 2: Copying verified data from backup to specified locations for the live site:
                            // Copy config.json and users.json
                            copy('temp_restore/conf/config.json', 'conf/config.json');
                            copy('temp_restore/users/users.json', 'users/users.json');

                            // Copy all JSON files from employees folder
                            $employeeFiles = glob('temp_restore/employees/*.json');
                            foreach ($employeeFiles as $file) {
                                $fileName = basename($file);
                                copy($file, 'employees/' . $fileName);
                            }

                            include "update_data.php";

                            // Copy all JPG image files from temp_restore/images/large/ to ../images/large/
                            $imageFiles = glob('temp_restore/images/large/*.jpg');
                            foreach ($imageFiles as $file) {
                                $fileName = basename($file);

                                // Copy to large folder
                                copy($file, '../images/large/' . $fileName);

                                // Create a resized copy in the med folder
                                $medDestination = '../images/med/' . $fileName;
                                resizeImage($file, $medDestination, 89, 89);

                                // Create a resized copy in the thumb folder
                                $thumbDestination = '../images/thumb/' . $fileName;
                                resizeImage($file, $thumbDestination, 50, 50);
                            }

                            // Step 3: Once restoration is done, remove temporary extracted files
                            deleteDirectory($extractPath);
            
                            echo '<p id="updateStatus" class="success">Data restored successfully!</p>';
                        } else {
                            echo '<p id="updateStatus" class="failure">Invalid backup file. Please ensure it matches the required structure.</p>';
                        }
                    } else {
                        echo '<p id="updateStatus" class="failure">Failed to open uploaded file.</p>';
                    }
                } else {
                    echo '<p id="updateStatus" class="failure">Error uploading file.</p>';
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
    
    <!-- JavaScript for toggling the instructions popup -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const toggleBackup = document.getElementById("toggleBackup");
            const toggleRestore = document.getElementById("toggleRestore");
            const backupPopupOverlay = document.getElementById("backupPopupOverlay");
            const restorePopupOverlay = document.getElementById("restorePopupOverlay");
            const resetPopupOverlay = document.getElementById("resetPopupOverlay");

            toggleBackup.addEventListener("click", function () {
                backupPopupOverlay.style.display = "block";
                restorePopupOverlay.style.display = "none";
                resetPopupOverlay.style.display = "none";
            });

            toggleRestore.addEventListener("click", function () {
                restorePopupOverlay.style.display = "block";
                backupPopupOverlay.style.display = "none";
                resetPopupOverlay.style.display = "none";
            });

            // Close buttons for backup and restore popups
            document.getElementById("closeBackupButton").addEventListener("click", function () {
                backupPopupOverlay.style.display = "none";
            });

            document.getElementById("closeRestoreButton").addEventListener("click", function () {
                restorePopupOverlay.style.display = "none";
            });
        });
    </script>
</body>
</html>
