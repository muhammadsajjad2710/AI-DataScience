<?php
// Database configuration
define('DB_HOST', '');  // or the IP address of the server
define('DB_PORT', '');       // Default PostgreSQL port
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASSWORD', '');

// Establish connection
function connectDb() {
    $conn = pg_connect("host=" . DB_HOST . " port=" . DB_PORT . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASSWORD);
    
    if (!$conn) {
        die("Error: Unable to connect to the database. " . pg_last_error());
    }
    
    return $conn;
}
?>
