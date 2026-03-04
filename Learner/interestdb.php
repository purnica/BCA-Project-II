<?php
// ─── 1. Database Connection ───────────────────────────────────────────────────
$servername = "localhost";
$username   = "root";
$password   = "Mysql@123";
$dbname     = "project6";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ─── 2. Validate Input ────────────────────────────────────────────────────────
if (!isset($_POST['courses']) || empty($_POST['courses'])) {
    die("No courses selected. Please go back and select at least one interest.");
}

$learner_id = (int) ($_GET['learner_id'] ?? 0);

if ($learner_id <= 0) {
    die("Invalid learner ID. Please try again.");
}

$courses = $_POST['courses'];

// ─── 3. Check if this is an UPDATE (learner already has interests) ───────────
// or an INSERT (new signup, first time selecting interests)
$check = $conn->prepare("
    SELECT COUNT(*) as count
    FROM learnerinterests
    WHERE learner_id = ?
");
$check->bind_param("i", $learner_id);
$check->execute();
$check_result = $check->get_result();
$row = $check_result->fetch_assoc();
$is_update = ($row['count'] > 0);
$check->close();

// ─── 4. DELETE old interests if updating ─────────────────────────────────────
if ($is_update) {
    $delete = $conn->prepare("DELETE FROM learnerinterests WHERE learner_id = ?");
    $delete->bind_param("i", $learner_id);
    $delete->execute();
    $delete->close();
}

// ─── 5. INSERT new/updated interests ─────────────────────────────────────────
$stmt = $conn->prepare("
    INSERT INTO learnerinterests (learner_id, interests)
    VALUES (?, ?)
");

foreach ($courses as $course) {
    $stmt->bind_param("is", $learner_id, $course);
    $stmt->execute();
}

$stmt->close();
$conn->close();

// ─── 6. Redirect based on context ────────────────────────────────────────────
// If updating (came from update.php), redirect back to update.php with success
// If new signup, redirect to login page

if ($is_update) {
    // Redirect to update.php with success message
    echo '<script>
            alert("Interests updated successfully!");
            window.location.href = "update.php";
          </script>';
} else {
    // New signup — redirect to login
    header("Location: http://localhost/bca-Project-II/webpage/login.php");
    exit;
}
?>