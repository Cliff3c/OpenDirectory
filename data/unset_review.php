<?php
// Start the session
session_start();

// Unset the session variable related to the employee review
if (isset($_SESSION['employee_review_in_progress'])) {
    unset($_SESSION['employee_review_in_progress']);
    //echo "Employee review in progress: No"; // Output a message or response
} else {
    //echo "Employee review in progress: Not set"; // Handle the case where the variable was not set
}
?>
