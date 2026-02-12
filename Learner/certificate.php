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

$learner_id  = (int) ($_SESSION['id'] ?? 0);
$first_name  = $_SESSION['firstname'] ?? '';
$last_name   = $_SESSION['lastname']  ?? '';          // use if you store lastname in session
$full_name   = trim($first_name . ' ' . $last_name);
if ($full_name === '') $full_name = $first_name;      // fallback to firstname only

// ─── 3. Get & Validate Course ID ─────────────────────────────────────────────
$course_id = (int) ($_GET['course_id'] ?? 0);

if ($course_id <= 0) {
    header("Location: EnrolledCourses.php");
    exit;
}

// ─── 4. Verify the learner is actually enrolled ───────────────────────────────
// Security check: learner should not be able to generate a certificate
// for a course they never enrolled in.
$enrolled = false;
if ($learner_id > 0) {
    $chk = $conn->prepare("
        SELECT enrollment_id, enrolled_at
        FROM enrollments
        WHERE learner_id = ? AND course_id = ?
    ");
    $chk->bind_param("ii", $learner_id, $course_id);
    $chk->execute();
    $chk->store_result();
    $chk->bind_result($enrollment_id_val, $enrolled_at_val);
    $chk->fetch();
    $enrolled       = ($chk->num_rows > 0);
    $enrollment_id  = $enrollment_id_val  ?? 0;
    $enrolled_date  = $enrolled_at_val    ?? date('Y-m-d H:i:s');
    $chk->close();
}

if (!$enrolled) {
    // Not enrolled — redirect away
    header("Location: courses.php");
    exit;
}

// ─── 5. Fetch Course Title ────────────────────────────────────────────────────
$stmt = $conn->prepare("SELECT title, category FROM course WHERE id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

if (!$course) {
    header("Location: EnrolledCourses.php");
    exit;
}

// ─── 6. Format date for certificate ──────────────────────────────────────────
$issue_date      = date('F d, Y', strtotime($enrolled_date));
$cert_number     = 'EDUGHAR-' . strtoupper(substr(md5($learner_id . $course_id), 0, 8));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate <?php echo htmlspecialchars($course['title']); ?></title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <!-- Google Fonts for certificate typography -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">

    <!-- Global CSS (navbar, sidebar, footer — DO NOT EDIT) -->
    <link rel="stylesheet" href="http://127.0.0.1:5500/Styles/style.css">

    <style>

        /* ── Page wrapper ── */
        .cert-page,
        .cert-page * { box-sizing: border-box; }

        .cert-page {
            padding: 28px 24px 48px;
            max-width: 1000px;
            margin: 0 auto;
            font-size: 16px;
        }

        /* ── Action bar (buttons above certificate) ── */
        .cert-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 24px;
        }

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

        .print-btn {
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
            transition: background 0.2s;
            text-decoration: none;
        }
        .print-btn:hover { background: #cf6d17; }

        /* ── Certificate Box ── */
        .certificate {
            background: #fff;
            border-radius: 4px;
            padding: 0;
            box-shadow: 0 8px 40px rgba(0,0,0,0.12);
            position: relative;
            overflow: hidden;
        }

        /* Outer gold border frame */
        .certificate::before {
            content: '';
            position: absolute;
            inset: 10px;
            border: 3px solid #d4af37;
            border-radius: 2px;
            pointer-events: none;
            z-index: 1;
        }

        /* Inner content area */
        .cert-inner {
            padding: 52px 64px;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        /* Top decorative line */
        .cert-topbar {
            height: 10px;
            background: linear-gradient(90deg, #e67e22, #f39c12, #e67e22);
        }

        /* Logo + site name row */
        .cert-logo-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 28px;
        }
        .cert-logo-row img {
            height: 44px;
        }
        .cert-logo-row span {
            font-family: 'Lato', sans-serif;
            font-size: 28px;
            font-weight: 700;
            color: #1a1a2e;
        }
        .cert-logo-row span em {
            color: var(--orange, #e67e22);
            font-style: normal;
        }

        /* Certificate of Completion heading */
        .cert-heading {
            font-family: 'Cinzel', serif;
            font-size: 14px;
            font-weight: 400;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            color: #888;
            margin-bottom: 6px;
        }
        .cert-title-main {
            font-family: 'Cinzel', serif;
            font-size: 34px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 24px;
        }

        /* Divider */
        .cert-divider {
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, #e67e22, #f39c12);
            margin: 0 auto 24px;
            border-radius: 2px;
        }

        /* "This certifies that" */
        .cert-presented-to {
            font-family: 'Lato', sans-serif;
            font-size: 15px;
            color: #888;
            margin-bottom: 8px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        /* Learner Name */
        .cert-name {
            font-family: 'Cinzel', serif;
            font-size: 42px;
            font-weight: 700;
            color: #1a5490;
            margin-bottom: 4px;
            line-height: 1.2;
        }

        /* Underline under name */
        .cert-name-underline {
            width: 320px;
            max-width: 80%;
            height: 2px;
            background: #d4af37;
            margin: 0 auto 24px;
        }

        /* "has successfully completed" */
        .cert-completed-text {
            font-family: 'Lato', sans-serif;
            font-size: 16px;
            color: #555;
            margin-bottom: 8px;
        }

        /* Course name */
        .cert-course-name {
            font-family: 'Cinzel', serif;
            font-size: 22px;
            font-weight: 600;
            color: #1a1a2e;
            margin-bottom: 4px;
            line-height: 1.4;
        }

        /* Category tag */
        .cert-category {
            display: inline-block;
            font-family: 'Lato', sans-serif;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #fff;
            background: var(--orange, #e67e22);
            padding: 4px 14px;
            border-radius: 50px;
            margin-bottom: 32px;
        }

        /* Bottom info row: date + cert number + signature */
        .cert-footer-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 24px;
            margin-top: 8px;
        }
        .cert-footer-item {
            text-align: center;
            flex: 1;
            min-width: 140px;
        }
        .cert-footer-item .label {
            font-family: 'Lato', sans-serif;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #a0aec0;
            margin-bottom: 4px;
        }
        .cert-footer-item .value {
            font-family: 'Lato', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #2d3748;
        }
        /* Signature line */
        .cert-signature-line {
            width: 120px;
            height: 2px;
            background: #2d3748;
            margin: 0 auto 4px;
        }

        /* Seal / watermark (decorative) */
        .cert-seal {
            position: absolute;
            right: 60px;
            bottom: 60px;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            border: 4px solid rgba(212,175,55,0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.2;
            font-size: 40px;
            color: #d4af37;
            z-index: 0;
            pointer-events: none;
        }

        /* Bottom orange bar */
        .cert-bottombar {
            height: 10px;
            background: linear-gradient(90deg, #e67e22, #f39c12, #e67e22);
        }

        /* ── Print Styles ── */
        @media print {
            /* Hide everything except the certificate */
            body > *:not(.cert-page) { display: none !important; }
            .cert-page .cert-actions { display: none !important; }
            .cert-page               { padding: 0; max-width: 100%; }
            .certificate             { box-shadow: none; }
            header, .side-bar, footer { display: none !important; }
        }

        /* ── Responsive ── */
        @media (max-width: 680px) {
            .cert-inner         { padding: 36px 28px; }
            .cert-title-main    { font-size: 24px; }
            .cert-name          { font-size: 30px; }
            .cert-course-name   { font-size: 17px; }
            .cert-footer-row    { justify-content: center; }
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
                <h3 class="name"><?php echo htmlspecialchars($first_name); ?></h3>
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
            <h3 class="name"><?php echo htmlspecialchars($first_name); ?></h3>
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
    <div class="cert-page">

        <!-- Action bar -->
        <div class="cert-actions">
            <a href="EnrolledCourses.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> My Enrolled Courses
            </a>
            <button class="print-btn" onclick="window.print()">
                <i class="fas fa-print"></i> Print / Save as PDF
            </button>
        </div>

        <!-- Certificate -->
        <div class="certificate">

            <!-- Top colour bar -->
            <div class="cert-topbar"></div>

            <div class="cert-inner">

                <!-- Decorative seal -->
                <div class="cert-seal">
                    <i class="fas fa-award"></i>
                </div>

                <!-- Logo row -->
                <div class="cert-logo-row">
                    <!-- If your logo path works, use the img tag;
                         otherwise a text fallback is shown -->
                    <img src="http://127.0.0.1:5500/Styles/logo1.png"
                         alt="EduGhar"
                         onerror="this.style.display='none'">
                    <span>Edu<em>Ghar.</em></span>
                </div>

                <!-- Certificate heading -->
                <p class="cert-heading">Certificate of Completion</p>
                <h1 class="cert-title-main">Achievement Award</h1>

                <div class="cert-divider"></div>

                <p class="cert-presented-to">This certifies that</p>

                <!-- Learner name -->
                <div class="cert-name"><?php echo htmlspecialchars($full_name); ?></div>
                <div class="cert-name-underline"></div>

                <p class="cert-completed-text">has successfully completed the course</p>

                <!-- Course name -->
                <div class="cert-course-name">
                    <?php echo htmlspecialchars($course['title']); ?>
                </div>
                <span class="cert-category">
                    <?php echo htmlspecialchars($course['category']); ?>
                </span>

                <!-- Footer row: date | cert no. | signature -->
                <div class="cert-footer-row">

                    <div class="cert-footer-item">
                        <div class="label">Date of Completion</div>
                        <div class="value"><?php echo $issue_date; ?></div>
                    </div>

                    <div class="cert-footer-item">
                        <div class="cert-signature-line"></div>
                        <div class="label">Authorised By</div>
                        <div class="value">EduGhar Team</div>
                    </div>

                    <div class="cert-footer-item">
                        <div class="label">Certificate No.</div>
                        <div class="value"><?php echo $cert_number; ?></div>
                    </div>

                </div><!-- /.cert-footer-row -->

            </div><!-- /.cert-inner -->

            <!-- Bottom colour bar -->
            <div class="cert-bottombar"></div>

        </div><!-- /.certificate -->

    </div><!-- /.cert-page -->

    <!-- ─── FOOTER (DO NOT EDIT) ─────────────────────────────────────────── -->
    <footer class="footer">
        &copy; copyright @ 2024 ALL RIGHTS RESERVED. <span>EduGhar </span>For SKILLS!
    </footer>

    <!-- Global JS (navbar, sidebar, dark-mode — DO NOT EDIT) -->
    <script src="http://127.0.0.1:5500/Styles/script.js"></script>

</body>
</html>