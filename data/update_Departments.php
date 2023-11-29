<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $configPath = '../data/conf/config.json';
    $configData = json_decode(file_get_contents($configPath), true);

    if ($configData !== null) {
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'add_department') {
                // Handle adding a new Department
                if (isset($_POST['newDepartment'])) {
                    $newDepartment = $_POST['newDepartment'];
                    $nextDepartmentKey = 'department_' . count($configData['organization_Settings']['Departments']);

                    // Add the new department
                    $configData['organization_Settings']['Departments'][$nextDepartmentKey] = $newDepartment;

                    // Save the changes to the JSON file
                    $success = file_put_contents($configPath, json_encode($configData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);

                    // Check if saving was successful and respond accordingly
                    if ($success !== false) {
                        // Respond with a success message and the updated departments
                        echo json_encode(['message' => 'Data updated successfully', 'Departments' => $configData['organization_Settings']['Departments']]);
                    } else {
                        echo json_encode(['error' => 'An error occurred while saving the data']);
                    }
                }
            } elseif ($_POST['action'] === 'remove_department') {
                // Debugging: Echo the entire $_POST data and departments array
                echo json_encode(['debug' => ['post_data' => $_POST, 'departments_array' => $configData['organization_Settings']['Departments']]]);
                
                // Handle removing a department
                $departmentKeyToRemove = $_POST['departmentKey'];
            
                // Check if the department key exists
                if (isset($configData['organization_Settings']['Departments'][$departmentKeyToRemove])) {
                    // Remove the department
                    unset($configData['organization_Settings']['Departments'][$departmentKeyToRemove]);
            
                    // Reorder the departments with consecutive numeric keys
                    $index = 0;
                    $updatedDepartments = [];
                    foreach ($configData['organization_Settings']['Departments'] as $department) {
                        $updatedDepartments['department_' . $index++] = $department;
                    }
            
                    // Save the changes to the JSON file
                    $configData['organization_Settings']['Departments'] = $updatedDepartments;
                    $success = file_put_contents($configPath, json_encode($configData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
            
                    // Check if saving was successful and respond accordingly
                    if ($success !== false) {
                        echo json_encode(['message' => 'Data updated successfully', 'Departments' => $configData['organization_Settings']['Departments']]);
                    } else {
                        echo json_encode(['error' => 'An error occurred while saving the data']);
                    }
                } else {
                    echo json_encode(['error' => 'Department not found in the array']);
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
