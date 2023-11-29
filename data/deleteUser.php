<?php
session_start();

function loadUserData() {
    $userDataFile = '../data/users/users.json';
    if (file_exists($userDataFile)) {
        $userData = json_decode(file_get_contents($userDataFile), true);
        if ($userData !== null) {
            return $userData;
        }
    }
    return [];
}

function saveUserData($userData) {
    $userDataFile = '../data/users/users.json';
    $jsonString = json_encode($userData, JSON_PRETTY_PRINT);
    file_put_contents($userDataFile, $jsonString, LOCK_EX);
}

if (isset($_POST['delete_user'])) {
    $userToDelete = $_POST['user_to_delete'];
    $userData = loadUserData();

    if (array_key_exists($userToDelete, $userData)) {
        unset($userData[$userToDelete]);
        saveUserData($userData);
        echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
}
?>
