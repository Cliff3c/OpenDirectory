<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $configPath = '../data/conf/config.json';
        $configData = json_decode(file_get_contents($configPath), true);

        if ($configData !== null) {
                if (isset($_POST['action'])) {
                    if ($_POST['action'] === 'add_title') {
                        // Handle adding a new title
                        if (isset($_POST['newTitle'])) {
                            $newTitle = $_POST['newTitle'];
                            $nextTitleKey = 'title_' . count($configData['organization_Settings']['titles']);

                            // Add the new title
                            $configData['organization_Settings']['titles'][$nextTitleKey] = $newTitle;

                            // Save the changes to the JSON file
                            $success = file_put_contents($configPath, json_encode($configData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);

                            // Check if saving was successful and respond accordingly
                            if ($success !== false) {
                                // Respond with a success message and the updated titles
                                echo json_encode(['message' => 'Data updated successfully', 'titles' => $configData['organization_Settings']['titles']]);
                            } else {
                                echo json_encode(['error' => 'An error occurred while saving the data']);
                            }
                        }
                    } elseif ($_POST['action'] === 'remove_title') {
                        // Debugging: Echo the entire $_POST data and titles array
                        echo json_encode(['debug' => ['post_data' => $_POST, 'titles_array' => $configData['organization_Settings']['titles']]]);
                        
                        // Handle removing a title
                        $titleKeyToRemove = $_POST['titleKey'];
                    
                        // Check if the title key exists
                        if (isset($configData['organization_Settings']['titles'][$titleKeyToRemove])) {
                            // Remove the title
                            unset($configData['organization_Settings']['titles'][$titleKeyToRemove]);
                    
                            // Reorder the titles with consecutive numeric keys
                            $index = 0;
                            $updatedTitles = [];
                            foreach ($configData['organization_Settings']['titles'] as $title) {
                                $updatedTitles['title_' . $index++] = $title;
                            }
                    
                            // Save the changes to the JSON file
                            $configData['organization_Settings']['titles'] = $updatedTitles;
                            $success = file_put_contents($configPath, json_encode($configData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
                    
                            // Check if saving was successful and respond accordingly
                            if ($success !== false) {
                                echo json_encode(['message' => 'Data updated successfully', 'titles' => $configData['organization_Settings']['titles']]);
                            } else {
                                echo json_encode(['error' => 'An error occurred while saving the data']);
                            }
                        } else {
                            echo json_encode(['error' => 'Title not found in the array']);
                        }
                    } else {
                        echo json_encode(['error' => 'Invalid request']);
                    }
            } else {
                // Handle invalid JSON data
                http_response_code(400);
                echo json_encode(['error' => 'Invalid JSON data']);
            }
        }   
    }
    // json_encode(['error' => 'Method not allowed']);
?>
