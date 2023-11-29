<?php
header('Access-Control-Allow-Origin: *'); // Allow requests from any origin
header('Content-Type: application/json');


error_log('Fetching titles request received');
$config = json_decode(file_get_contents('conf' . DIRECTORY_SEPARATOR . 'config.json'), true);
$titles = $config['organization_Settings']['titles'];

// Log the response before sending it
error_log('Response: ' . json_encode(['titles' => $titles]));

echo json_encode(['titles' => $titles]);
?>
