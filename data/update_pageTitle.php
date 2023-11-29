<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $configPath = '../data/conf/config.json';
    $configData = json_decode(file_get_contents($configPath), true);

    if ($configData !== null) {
        if (isset($_POST['pageTitle'])) {
            $newTitle = $_POST['pageTitle'];
            $configData['pageTitle'] = $newTitle;

            // Save the changes to the JSON file
            $success = file_put_contents($configPath, json_encode($configData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);

            // Check if saving was successful and respond accordingly
            if ($success !== false) {
                echo json_encode(['message' => 'Page title updated successfully']);
            } else {
                echo json_encode(['error' => 'An error occurred while saving the data']);
            }
        } else {
            echo json_encode(['error' => 'Invalid request']);
        }
    }
}
?>
