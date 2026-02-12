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
?>

<?php
// ─── 2. Session Check ─────────────────────────────────────────────────────────
session_start();

if (!isset($_SESSION['firstname'])) {
    header("Location: login.php");
    exit;
}

$name       = $_SESSION['firstname'];
$learner_id = (int) ($_SESSION['id'] ?? 0);

// ─── 3. Get & Validate Course ID from URL ────────────────────────────────────
$course_id = (int) ($_GET['id'] ?? 0);

if ($course_id <= 0) {
    header("Location: courses.php");
    exit;
}

// ─── 4. Handle Enroll POST Request ───────────────────────────────────────────
// Must run BEFORE any HTML so headers can still be sent.
$enroll_status = ''; // values: 'new' | 'already' | 'error' | ''

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'enroll') {

    if ($learner_id <= 0) {
        $enroll_status = 'error';
    } else {
        // Check: is learner already enrolled?
        $check = $conn->prepare("
            SELECT enrollment_id FROM enrollments
            WHERE learner_id = ? AND course_id = ?
        ");
        $check->bind_param("ii", $learner_id, $course_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $enroll_status = 'already'; // do NOT insert again
        } else {
            $ins = $conn->prepare("
                INSERT INTO enrollments (learner_id, course_id, enrolled_at)
                VALUES (?, ?, NOW())
            ");
            $ins->bind_param("ii", $learner_id, $course_id);
            $enroll_status = $ins->execute() ? 'new' : 'error';
            $ins->close();
        }
        $check->close();
    }
}

// ─── 5. Fetch Course ─────────────────────────────────────────────────────────
$stmt = $conn->prepare("SELECT * FROM course WHERE id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$course) {
    header("Location: courses.php");
    exit;
}

// ─── 6. Fetch Chapters ───────────────────────────────────────────────────────
$stmt2 = $conn->prepare("
    SELECT content_id, content_title, filepath
    FROM course_content
    WHERE course_id = ?
    ORDER BY content_id ASC
");
$stmt2->bind_param("i", $course_id);
$stmt2->execute();
$chapters = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt2->close();

// ─── 7. Check if Already Enrolled (to set button state on page load) ─────────
$already_enrolled = false;
if ($learner_id > 0) {
    $chk = $conn->prepare("
        SELECT enrollment_id FROM enrollments
        WHERE learner_id = ? AND course_id = ?
    ");
    $chk->bind_param("ii", $learner_id, $course_id);
    $chk->execute();
    $chk->store_result();
    $already_enrolled = ($chk->num_rows > 0);
    $chk->close();
}

$conn->close();

// ─── 8. Helper: Detect file type from extension ──────────────────────────────
function getFileType(string $path): string {
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) return 'image';
    if (in_array($ext, ['mp4','webm','ogg','avi','mov']))  return 'video';
    if ($ext === 'pdf')                                    return 'pdf';
    return 'file';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['title']); ?> – EduGhar</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

    <!-- Global CSS (navbar, sidebar, footer — DO NOT EDIT) -->
    <link rel="stylesheet" href="http://127.0.0.1:5500/Styles/style.css">

    <style>

        /* ── Box-sizing scoped reset ── */
        .detail-section,
        .detail-section * { box-sizing: border-box; }

        /* ── Page Wrapper ── */
        .detail-section {
            padding: 28px 24px 48px;
            max-width: 960px;
            margin: 0 auto;
            font-size: 16px;
        }

        /* ── Back Link ── */
        .back-row { margin-bottom: 20px; }
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 14px;
            font-weight: 600;
            color: var(--orange, #e67e22);
            text-decoration: none;
            transition: color 0.2s;
        }
        .back-btn:hover { color: #cf6d17; }

        /* ── White Card ── */
        .detail-card {
            background: #fff;
            border-radius: 12px;
            padding: 28px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            margin-bottom: 24px;
        }

        /* ── Card Header ── */
        .detail-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 2px solid #e2e8f0;
            flex-wrap: wrap;
        }
        .detail-card-header h2 {
            font-size: 24px;
            font-weight: 700;
            color: #1a5490;
            margin: 0;
            line-height: 1.3;
        }
        .category-badge {
            display: inline-block;
            padding: 5px 14px;
            border-radius: 20px;
            background: #ebf8ff;
            color: #2b6cb0;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            flex-shrink: 0;
        }

        /* ── Thumbnail ── */
        .course-thumbnail {
            width: 100%;
            max-width: 400px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            display: block;
            margin-bottom: 24px;
            object-fit: cover;
        }
        .thumb-placeholder {
            width: 100%;
            max-width: 400px;
            height: 200px;
            border-radius: 10px;
            background: linear-gradient(135deg, #e67e22 0%, #f39c12 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 56px;
            color: #fff;
            margin-bottom: 24px;
        }

        /* ── Field Block ── */
        .field-block { margin-bottom: 22px; }
        .field-block label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #718096;
            margin-bottom: 6px;
        }
        .field-block p {
            font-size: 15px;
            color: #2d3748;
            line-height: 1.65;
            margin: 0;
        }

        /* ── Learning Outcomes ── */
        .outcomes-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .outcomes-list li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 15px;
            color: #2d3748;
            line-height: 1.5;
        }
        .outcomes-list li::before {
            content: '✓';
            color: #48bb78;
            font-weight: 700;
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* ── Section Title ── */
        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a5490;
            margin-bottom: 16px;
        }
        .section-title .chapter-count {
            font-size: 14px;
            font-weight: 400;
            color: #718096;
            margin-left: 8px;
        }

        /* ── Chapter Row ── */
        .chapter-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            margin-bottom: 10px;
            background: #f7fafc;
            transition: background 0.15s;
            flex-wrap: wrap;
        }
        .chapter-item:hover { background: #ebf8ff; }
        .ch-number {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #1a5490;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            flex-shrink: 0;
        }
        .ch-info { flex: 1; min-width: 0; }
        .ch-title { font-size: 15px; font-weight: 600; color: #2d3748; margin-bottom: 2px; }
        .ch-path  { font-size: 12px; color: #a0aec0; word-break: break-all; }
        .ch-type-badge {
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            flex-shrink: 0;
        }
        .type-image { background: #c6f6d5; color: #276749; }
        .type-video { background: #fed7d7; color: #742a2a; }
        .type-pdf   { background: #fefcbf; color: #744210; }
        .type-file  { background: #e2e8f0; color: #4a5568; }
        .ch-link {
            font-size: 13px;
            font-weight: 600;
            color: #2b6cb0;
            text-decoration: none;
            flex-shrink: 0;
            transition: color 0.2s;
        }
        .ch-link:hover { color: #1a5490; text-decoration: underline; }
        .no-chapters { font-size: 15px; color: #a0aec0; padding: 10px 0; }

        /* ── Enroll Button Row ── */
        .enroll-wrap {
            margin-top: 8px;
            display: flex;
            justify-content: flex-end;
        }
        /* Orange — not yet enrolled */
        .enroll-btn-detail {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 32px;
            background: var(--orange, #e67e22);
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            border-radius: 10px;
            cursor: pointer;
            border: none;
            transition: background 0.2s, transform 0.15s;
            letter-spacing: 0.02em;
        }
        .enroll-btn-detail:hover { background: #cf6d17; transform: scale(1.02); }
        /* Green — already enrolled */
        .enrolled-badge-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 32px;
            background: #48bb78;
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            border-radius: 10px;
            cursor: default;
            border: none;
            letter-spacing: 0.02em;
        }

        /* ── Popup Overlay ── */
        .popup-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        .popup-overlay.active { display: flex; }
        .popup-box {
            background: #fff;
            border-radius: 16px;
            padding: 40px 36px;
            max-width: 440px;
            width: 90%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            animation: popIn 0.3s ease;
        }
        @keyframes popIn {
            from { transform: scale(0.85); opacity: 0; }
            to   { transform: scale(1);    opacity: 1; }
        }
        .popup-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 32px;
            color: #fff;
        }
        .popup-icon.success { background: linear-gradient(135deg, #48bb78, #38a169); }
        .popup-icon.info    { background: linear-gradient(135deg, #63b3ed, #3182ce); }
        .popup-icon.warning { background: linear-gradient(135deg, #ed8936, #dd6b20); }
        .popup-box h3 {
            font-size: 22px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 10px;
        }
        .popup-box p {
            font-size: 15px;
            color: #718096;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        /* Two-button row inside popup */
        .popup-btn-row {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        /* Orange primary button */
        .popup-btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 24px;
            background: var(--orange, #e67e22);
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s;
        }
        .popup-btn-primary:hover { background: #cf6d17; }
        /* Ghost secondary button */
        .popup-btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 24px;
            background: transparent;
            color: #718096;
            font-size: 15px;
            font-weight: 600;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            cursor: pointer;
            text-decoration: none;
            transition: border-color 0.2s, color 0.2s;
        }
        .popup-btn-ghost:hover { border-color: #cbd5e0; color: #4a5568; }

        /* ── Dark Mode ── */
        body.dark .detail-card         { background: #1e1e2e; box-shadow: 0 2px 12px rgba(0,0,0,0.35); }
        body.dark .detail-card-header h2 { color: #90cdf4; }
        body.dark .field-block p,
        body.dark .ch-title,
        body.dark .outcomes-list li    { color: #e2e8f0; }
        body.dark .field-block label,
        body.dark .ch-path             { color: #a0aec0; }
        body.dark .chapter-item        { background: #2d2d3f; border-color: #3a3a50; }
        body.dark .chapter-item:hover  { background: #383850; }
        body.dark .popup-box           { background: #1e1e2e; }
        body.dark .popup-box h3        { color: #f0f0f0; }
        body.dark .popup-box p         { color: #a0aec0; }

        /* ── Responsive ── */
        @media (max-width: 600px) {
            .detail-card-header h2          { font-size: 19px; }
            .section-title                  { font-size: 16px; }
            .enroll-wrap                    { justify-content: stretch; }
            .enroll-btn-detail,
            .enrolled-badge-btn             { width: 100%; justify-content: center; }
            .chapter-item                   { gap: 10px; }
            .popup-btn-row                  { flex-direction: column; }
            .popup-btn-primary,
            .popup-btn-ghost                { width: 100%; justify-content: center; }
        }

    </style>
</head>

<body>

    <!-- ─── HEADER (DO NOT EDIT) ─────────────────────────────────────────── -->
    <header class="header">
        <section class="flex">
            <a href="home.php">
                <img src="http://127.0.0.1:5500/Styles/logo1.png" class="logo" alt="EduGhar">
            </a>
            <div class="icons">
                <div id="menu-btn"   class="fas fa-bars"></div>
                <div id="search-btn" class="fas fa-search"></div>
                <div id="user-btn"   class="fas fa-user"></div>
                <div id="toggle-btn" class="fas fa-sun"></div>
            </div>
            <div class="profile">
                <img src="http://127.0.0.1:5500/Styles/pic-5.jpg" class="image" alt="">
                <h3 class="name"><?php echo htmlspecialchars($name); ?></h3>
                <p class="role">Student</p>
                <a href="update.php" class="btn">view profile</a>
                <div class="flex-btn">
                    <a href="http://localhost/bCA-Project-II/styles/logout.php" class="option-btn">logout</a>
                </div>
            </div>
        </section>
    </header>

    <!-- ─── SIDEBAR (DO NOT EDIT) ────────────────────────────────────────── -->
    <div class="side-bar">
        <div id="close-btn"><i class="fas fa-times"></i></div>
        <div class="profile">
            <img src="http://127.0.0.1:5500/Styles/pic-5.jpg" class="image" alt="">
            <h3 class="name"><?php echo htmlspecialchars($name); ?></h3>
            <p class="role">student</p>
        </div>
        <nav class="navbar">
            <a href="home.php"><i class="fas fa-home"></i><span>home</span></a>
            <a href="about.php"><i class="fas fa-question"></i><span>about</span></a>
            <a href="courses.php"><i class="fas fa-graduation-cap"></i><span>courses</span></a>
            <a href="EnrolledCourses.php"><i class="fas fa-bookmark"></i><span>my courses</span></a>
            <a href="contact.php"><i class="fas fa-headset"></i><span>contact us</span></a>
        </nav>
    </div>

    <!-- ─── MAIN CONTENT ─────────────────────────────────────────────────── -->
    <section class="detail-section">

        <div class="back-row">
            <a href="courses.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Courses
            </a>
        </div>

        <!-- Card 1: Course Info -->
        <div class="detail-card">
            <div class="detail-card-header">
                <h2><?php echo htmlspecialchars($course['title']); ?></h2>
                <span class="category-badge">
                    <?php echo htmlspecialchars($course['category']); ?>
                </span>
            </div>

            <?php if (!empty($course['image'])) : ?>
                <img
                    src="<?php echo htmlspecialchars($course['image']); ?>"
                    alt="<?php echo htmlspecialchars($course['title']); ?>"
                    class="course-thumbnail"
                    onerror="this.style.display='none';
                             this.nextElementSibling.style.display='flex';">
                <div class="thumb-placeholder" style="display:none;">
                    <i class="fas fa-book"></i>
                </div>
            <?php else : ?>
                <div class="thumb-placeholder">
                    <i class="fas fa-book"></i>
                </div>
            <?php endif; ?>

            <div class="field-block">
                <label>Description</label>
                <p><?php echo nl2br(htmlspecialchars($course['primary_description'] ?? '')); ?></p>
            </div>

            <?php if (!empty($course['learning_outcomes'])) : ?>
                <div class="field-block">
                    <label>What You Will Learn</label>
                    <ul class="outcomes-list">
                        <?php foreach (explode(',', $course['learning_outcomes']) as $outcome) : ?>
                            <?php $outcome = trim($outcome); ?>
                            <?php if ($outcome !== '') : ?>
                                <li><?php echo htmlspecialchars($outcome); ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <!-- Card 2: Chapters -->
        <div class="detail-card">
            <div class="section-title">
                <i class="fas fa-list-ul" style="color:#e67e22;margin-right:8px;"></i>
                Course Content
                <span class="chapter-count">
                    (<?php echo count($chapters); ?>
                    chapter<?php echo count($chapters) !== 1 ? 's' : ''; ?>)
                </span>
            </div>

            <?php if (empty($chapters)) : ?>
                <p class="no-chapters">No chapters have been added yet.</p>
            <?php else : ?>
                <?php foreach ($chapters as $i => $ch) :
                    $ftype = getFileType($ch['filepath']);
                ?>
                <div class="chapter-item">
                    <div class="ch-number"><?php echo $i + 1; ?></div>
                    <div class="ch-info">
                        <div class="ch-title"><?php echo htmlspecialchars($ch['content_title']); ?></div>
                        <div class="ch-path"><?php echo htmlspecialchars($ch['filepath']); ?></div>
                    </div>
                    <span class="ch-type-badge type-<?php echo $ftype; ?>">
                        <?php echo $ftype; ?>
                    </span>
                    <a href="<?php echo htmlspecialchars($ch['filepath']); ?>"
                       target="_blank" class="ch-link">Open &nearr;</a>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Enroll Button -->
        <div class="enroll-wrap">
            <?php if ($already_enrolled) : ?>
                <button class="enrolled-badge-btn" disabled>
                    <i class="fas fa-check-circle"></i> Already Enrolled
                </button>
            <?php else : ?>
                <form method="POST"
                      action="course_detail.php?id=<?php echo $course_id; ?>"
                      style="margin:0;">
                    <input type="hidden" name="action" value="enroll">
                    <button type="submit" class="enroll-btn-detail">
                        <i class="fas fa-graduation-cap"></i> Enroll Now
                    </button>
                </form>
            <?php endif; ?>
        </div>

    </section>

    <!-- ─── POPUPS (server-rendered based on enroll_status) ─────────────── -->

    <?php if ($enroll_status === 'new') : ?>
    <div class="popup-overlay active" id="enrollPopup">
        <div class="popup-box">
            <div class="popup-icon success"><i class="fas fa-check"></i></div>
            <h3>Successfully Enrolled!</h3>
            <p>
                You have enrolled in<br>
                <strong><?php echo htmlspecialchars($course['title']); ?></strong>.<br>
                Happy learning! 🎉
            </p>
            <div class="popup-btn-row">
                <a href="certificate.php?course_id=<?php echo $course_id; ?>"
                   class="popup-btn-primary">
                    <i class="fas fa-certificate"></i> Generate Certificate
                </a>
                <button class="popup-btn-ghost" onclick="closePopup()">Continue</button>
            </div>
        </div>
    </div>

    <?php elseif ($enroll_status === 'already') : ?>
    <div class="popup-overlay active" id="enrollPopup">
        <div class="popup-box">
            <div class="popup-icon info"><i class="fas fa-info"></i></div>
            <h3>Already Enrolled</h3>
            <p>
                You are already enrolled in<br>
                <strong><?php echo htmlspecialchars($course['title']); ?></strong>.
            </p>
            <div class="popup-btn-row">
                <a href="certificate.php?course_id=<?php echo $course_id; ?>"
                   class="popup-btn-primary">
                    <i class="fas fa-certificate"></i> Generate Certificate
                </a>
                <button class="popup-btn-ghost" onclick="closePopup()">Close</button>
            </div>
        </div>
    </div>

    <?php elseif ($enroll_status === 'error') : ?>
    <div class="popup-overlay active" id="enrollPopup">
        <div class="popup-box">
            <div class="popup-icon warning">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3>Enrollment Failed</h3>
            <p>Something went wrong. Please log in again and retry.</p>
            <div class="popup-btn-row">
                <button class="popup-btn-ghost" onclick="closePopup()">Close</button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ─── FOOTER (DO NOT EDIT) ─────────────────────────────────────────── -->
    <footer class="footer">
        &copy; copyright @ 2024 ALL RIGHTS RESERVED. <span>EduGhar </span>For SKILLS!
    </footer>

    <!-- Global JS (navbar, sidebar, dark-mode — DO NOT EDIT) -->
    <script src="http://127.0.0.1:5500/Styles/script.js"></script>

    <script>
        function closePopup() {
            var p = document.getElementById('enrollPopup');
            if (p) p.classList.remove('active');
        }
        var overlay = document.getElementById('enrollPopup');
        if (overlay) {
            overlay.addEventListener('click', function (e) {
                if (e.target === this) closePopup();
            });
        }
    </script>

</body>
</html>