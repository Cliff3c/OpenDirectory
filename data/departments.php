<?php
header('Access-Control-Allow-Origin: *'); // Allow requests from any origin
header('Content-Type: application/json');


error_log('Fetching Departments request received');
$config = json_decode(file_get_contents('conf' . DIRECTORY_SEPARATOR . 'config.json'), true);
$Departments = $config['organization_Settings']['Departments'];

// Log the response before sending it
error_log('Response: ' . json_encode(['Departments' => $Departments]));

echo json_encode(['Departments' => $Departments]);
?>