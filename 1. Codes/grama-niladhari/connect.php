
<?php
// connect.php - Updated version
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); 
define('DB_PASS', ''); // Your password here
define('DB_NAME', 'resident_database');
define('DB_PORT', 3307);

// Create connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}
?>