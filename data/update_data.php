<?php
// Specify the directory where the employee JSON files are stored
$directory = 'employees/';

// Initialize an empty array to store the employee data
$employeeData = [];

// Get a list of JSON files in the directory
$jsonFiles = glob($directory . '*.json');

foreach ($jsonFiles as $jsonFile) {
    $jsonContent = file_get_contents($jsonFile);
    $jsonData = json_decode($jsonContent, true);

    // Check if the JSON data is valid
    if ($jsonData !== null) {
        // Append the valid JSON data to the employeeData array
        $employeeData[] = $jsonData;
    } else {
        // Handle invalid JSON data (optional)
        echo "Invalid JSON file: " . basename($jsonFile) . "<br>";
    }
}

// Create the final JSON structure
$jsonData = [
    "results" => $employeeData,
    "info" => [
        "results" => count($employeeData),
        "pages" => 1,
        "version" => "1.0"
    ]
];

// Encode the final JSON data
$jsonString = json_encode($jsonData, JSON_PRETTY_PRINT);

// Clear the contents of the employees.json file and write the new data
file_put_contents('employees.json', $jsonString, LOCK_EX);

echo "<p class='success'>Employees JSON file updated successfully!</p>";
?>
