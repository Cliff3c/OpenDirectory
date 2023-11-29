<?php
header('Access-Control-Allow-Origin: *'); // Allow requests from any origin
header('Content-Type: application/json');

$configPath = '../data/conf/config.json';
$configData = json_decode(file_get_contents($configPath), true);

if ($configData !== null) {
    $pageTitle = isset($configData['pageTitle']) ? $configData['pageTitle'] : 'Page title not set. Please update setting.';
    echo json_encode(['title' => $pageTitle]);
} else {
    echo json_encode(['title' => 'Page title not set. Please update setting.']);
}
?>
