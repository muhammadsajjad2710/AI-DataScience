<?php
$host = "localhost";
$dbname = "my_database";
$username = "root";
$password = "";


// Create a connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("<p style='color:red;'>❌ Database connection failed: " . $conn->connect_error . "</p>");
} else {
    #echo "<p style='color:green;'>✔ Database connected successfully!</p>";
}
?>

