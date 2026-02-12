<!-- database connectivity -->
<?php
$servername = "localhost";
$username = "root";
$pass = "Mysql@123";
$dbname = "file";

// Create connection
$conn = new mysqli($servername, $username, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
