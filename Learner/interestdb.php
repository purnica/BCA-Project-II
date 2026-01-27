<?php
// database connectivity 
$servername = "localhost";
$username = "root";
$pass = "Mysql@123";
$dbname = "project6";

// Create connection
$conn = new mysqli($servername, $username, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully";

if (!isset($_POST['courses']) || empty($_POST['courses'])) {
    die("No courses selected");
}

$learner_id = $_GET['learner_id'];
$courses = $_POST['courses'];

$stmt = $conn->prepare(
    "INSERT INTO learnerinterests (learner_id, interests) VALUES (?, ?)"
);

foreach ($courses as $course) {
    $stmt->bind_param("is", $learner_id, $course);
    $stmt->execute();
}

header("Location: http://localhost/bca-Project-II/webpage/login.php");
exit;

$stmt->close();
$conn->close();
?>