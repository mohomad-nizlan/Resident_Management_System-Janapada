
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Add this at the top of all protected pages:
// require_once 'auth_check.php';
?>