<?php
// ─── 1. Get learner_id from URL (passed from update.php) ─────────────────────
$learner_id = (int) ($_GET['learner_id'] ?? 0);

// ─── 2. Fetch existing interests if learner_id is provided ───────────────────
$existing_interests = [];

if ($learner_id > 0) {
    $servername = "localhost";
    $username   = "root";
    $password   = "Mysql@123";
    $dbname     = "project6";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if (!$conn->connect_error) {
        $stmt = $conn->prepare("
            SELECT interests
            FROM learnerinterests
            WHERE learner_id = ?
        ");
        $stmt->bind_param("i", $learner_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $existing_interests[] = strtolower(trim($row['interests']));
        }

        $stmt->close();
        $conn->close();
    }
}

// ─── 3. Helper function: is this interest already selected? ──────────────────
function isSelected($value, $existing) {
    return in_array(strtolower(trim($value)), $existing);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $learner_id > 0 ? 'Update' : 'Select'; ?> Your Interests – EduGhar</title>
    <link rel="stylesheet" href="style/learnerinterest.css">
</head>
<body>

<form action="interestdb.php?learner_id=<?php echo $learner_id; ?>"
      method="POST"
      class="container">

    <h2><?php echo $learner_id > 0 ? 'Update your interests:' : 'Pick your interests:'; ?></h2>

    <div class="options">

        <label class="pill">
            <input type="checkbox" name="courses[]" value="python"
                   <?php if (isSelected('python', $existing_interests)) echo 'checked'; ?>>
            Python
        </label>

        <label class="pill">
            <input type="checkbox" name="courses[]" value="machine learning"
                   <?php if (isSelected('machine learning', $existing_interests)) echo 'checked'; ?>>
            Machine Learning
        </label>

        <label class="pill">
            <input type="checkbox" name="courses[]" value="data science"
                   <?php if (isSelected('data science', $existing_interests)) echo 'checked'; ?>>
            Data Science
        </label>

        <label class="pill">
            <input type="checkbox" name="courses[]" value="web development"
                   <?php if (isSelected('web development', $existing_interests)) echo 'checked'; ?>>
            Web Development
        </label>

        <label class="pill">
            <input type="checkbox" name="courses[]" value="Artificial Intelligence"
                   <?php if (isSelected('Artificial Intelligence', $existing_interests)) echo 'checked'; ?>>
            Artificial Intelligence
        </label>

        <label class="pill">
            <input type="checkbox" name="courses[]" value="Cloud Computing"
                   <?php if (isSelected('Cloud Computing', $existing_interests)) echo 'checked'; ?>>
            Cloud Computing
        </label>

        <label class="pill">
            <input type="checkbox" name="courses[]" value="Computer Networks"
                   <?php if (isSelected('Computer Networks', $existing_interests)) echo 'checked'; ?>>
            Computer Networks
        </label>

        <label class="pill">
            <input type="checkbox" name="courses[]" value="Design"
                   <?php if (isSelected('Design', $existing_interests)) echo 'checked'; ?>>
            Design
        </label>

        <label class="pill">
            <input type="checkbox" name="courses[]" value="Cyber Security"
                   <?php if (isSelected('Cyber Security', $existing_interests)) echo 'checked'; ?>>
            Cyber Security
        </label>

        <label class="pill">
            <input type="checkbox" name="courses[]" value="Mobile App Development"
                   <?php if (isSelected('Mobile App Development', $existing_interests)) echo 'checked'; ?>>
            Mobile App Development
        </label>

        <label class="pill">
            <input type="checkbox" name="courses[]" value="UI/UX Design"
                   <?php if (isSelected('UI/UX Design', $existing_interests)) echo 'checked'; ?>>
            UI/UX Design
        </label>

        <label class="pill">
            <input type="checkbox" name="courses[]" value="Data Analytics"
                   <?php if (isSelected('Data Analytics', $existing_interests)) echo 'checked'; ?>>
            Data Analytics
        </label>

        <label class="pill">
            <input type="checkbox" name="courses[]" value="Arts"
                   <?php if (isSelected('Arts', $existing_interests)) echo 'checked'; ?>>
            Arts
        </label>

        <label class="pill">
            <input type="checkbox" name="courses[]" value="Clothing Designing"
                   <?php if (isSelected('Clothing Designing', $existing_interests)) echo 'checked'; ?>>
            Clothing Designing
        </label>

        <label class="pill">
            <input type="checkbox" name="courses[]" value="Banking"
                   <?php if (isSelected('Banking', $existing_interests)) echo 'checked'; ?>>
            Banking
        </label>

        <label class="pill">
            <input type="checkbox" name="courses[]" value="Marketing"
                   <?php if (isSelected('Marketing', $existing_interests)) echo 'checked'; ?>>
            Marketing
        </label>

        <label class="pill">
            <input type="checkbox" name="courses[]" value="Finance"
                   <?php if (isSelected('Finance', $existing_interests)) echo 'checked'; ?>>
            Finance
        </label>

        <label class="pill">
            <input type="checkbox" name="courses[]" value="Personality Development"
                   <?php if (isSelected('Personality Development', $existing_interests)) echo 'checked'; ?>>
            Personality Development
        </label>

        <label class="pill">
            <input type="checkbox" name="courses[]" value="Photography"
                   <?php if (isSelected('Photography', $existing_interests)) echo 'checked'; ?>>
            Photography
        </label>

        <label class="pill">
            <input type="checkbox" name="courses[]" value="Leadership Skills"
                   <?php if (isSelected('Leadership Skills', $existing_interests)) echo 'checked'; ?>>
            Leadership Skills
        </label>

        <label class="pill">
            <input type="checkbox" name="courses[]" value="Communication Skills"
                   <?php if (isSelected('Communication Skills', $existing_interests)) echo 'checked'; ?>>
            Communication Skills
        </label>

        <label class="pill">
            <input type="checkbox" name="courses[]" value="Physiotherapy"
                   <?php if (isSelected('Physiotherapy', $existing_interests)) echo 'checked'; ?>>
            Physiotherapy
        </label>

        <label class="pill">
            <input type="checkbox" name="courses[]" value="Phycology"
                   <?php if (isSelected('Phycology', $existing_interests)) echo 'checked'; ?>>
            Phycology
        </label>

        <label class="pill">
            <input type="checkbox" name="courses[]" value="Time Management"
                   <?php if (isSelected('Time Management', $existing_interests)) echo 'checked'; ?>>
            Time Management
        </label>

        <label class="pill">
            <input type="checkbox" name="courses[]" value="Project Management"
                   <?php if (isSelected('Project Management', $existing_interests)) echo 'checked'; ?>>
            Project Management
        </label>

        <label class="pill">
            <input type="checkbox" name="courses[]" value="Digital Marketing"
                   <?php if (isSelected('Digital Marketing', $existing_interests)) echo 'checked'; ?>>
            Digital Marketing
        </label>

        <label class="pill">
            <input type="checkbox" name="courses[]" value="Skills Development"
                   <?php if (isSelected('Skills Development', $existing_interests)) echo 'checked'; ?>>
            Skills Development
        </label>

        <label class="pill">
            <input type="checkbox" name="courses[]" value="Other"
                   <?php if (isSelected('Other', $existing_interests)) echo 'checked'; ?>>
            Other
        </label>

    </div>

    <button type="submit">
        <?php echo $learner_id > 0 ? 'Update Interests' : 'Submit'; ?>
    </button>

</form>

</body>
</html>