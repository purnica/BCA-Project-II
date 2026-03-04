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

// ─── 3. Fetch Only Enrolled Courses for this Learner ─────────────────────────
$enrolled_courses = [];

if ($learner_id > 0) {
    $stmt = $conn->prepare("
        SELECT
            c.id,
            c.title,
            c.category,
            c.primary_description,
            c.image,
            e.enrolled_at
        FROM enrollments e
        INNER JOIN course c ON e.course_id = c.id
        WHERE e.learner_id = ?
        ORDER BY e.enrolled_at DESC
    ");
    $stmt->bind_param("i", $learner_id);
    $stmt->execute();
    $enrolled_courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Enrolled Courses – EduGhar</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

    <!-- Global CSS (navbar, sidebar, footer — DO NOT EDIT) -->
    <link rel="stylesheet" href="http://127.0.0.1:5500/Styles/style.css">

    <style>

        /* ── Box-sizing scoped reset ── */
        .enrolled-section,
        .enrolled-section * { box-sizing: border-box; }

        /* ── Page Wrapper ── */
        .enrolled-section {
            padding: 28px 24px 48px;
            max-width: 1200px;
            margin: 0 auto;
            font-size: 16px;
        }

        /* ── Section Header ── */
        .enrolled-section .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 6px;
        }
        .enrolled-section .section-header i {
            font-size: 26px;
            color: var(--orange, #e67e22);
        }
        .enrolled-section h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--black, #1a1a2e);
            margin: 0;
        }
        .enrolled-section .section-sub {
            font-size: 15px;
            color: var(--light-color, #888);
            margin-bottom: 28px;
            padding-left: 38px;
        }

        /* ── Cards Grid ── */
        .enrolled-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
        }

        /* ── Course Card ── */
        .rec-card {
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 18px rgba(0,0,0,0.08);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            display: flex;
            flex-direction: column;
        }
        .rec-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 28px rgba(0,0,0,0.13);
        }

        /* ── Thumbnail ── */
        .rec-card .card-thumb {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background: #f0f0f0;
            display: block;
        }
        .rec-card .card-thumb-placeholder {
            width: 100%;
            height: 180px;
            background: linear-gradient(135deg, #e67e22 0%, #f39c12 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: #fff;
        }

        /* ── Card Body ── */
        .rec-card .card-body {
            padding: 16px 18px 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        /* ── Category Badge ── */
        .rec-card .card-category {
            display: inline-block;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #fff;
            background: var(--orange, #e67e22);
            padding: 4px 12px;
            border-radius: 50px;
            width: fit-content;
        }

        /* ── Title ── */
        .rec-card .card-title {
            font-size: 17px;
            font-weight: 700;
            color: var(--black, #1a1a2e);
            line-height: 1.4;
            margin: 0;
        }

        /* ── Description ── */
        .rec-card .card-desc {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
            flex: 1;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* ── Enrolled date row ── */
        .rec-card .enrolled-date {
            font-size: 12px;
            color: #a0aec0;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* ── Button row: two buttons ── */
        .rec-card .card-btn-row {
            display: flex;
            gap: 8px;
            margin-top: 4px;
        }

        /* View Course button (orange) */
        .rec-card .view-btn {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 0;
            background: var(--orange, #e67e22);
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.2s;
        }
        .rec-card .view-btn:hover { background: #cf6d17; }

        /* Certificate button (outlined) */
        .rec-card .cert-btn {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 0;
            background: transparent;
            color: var(--orange, #e67e22);
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            border: 2px solid var(--orange, #e67e22);
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
        }
        .rec-card .cert-btn:hover {
            background: var(--orange, #e67e22);
            color: #fff;
        }

        /* ── Empty State ── */
        .enrolled-empty {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 16px;
            color: #888;
        }
        .enrolled-empty i {
            font-size: 52px;
            margin-bottom: 16px;
            display: block;
            color: #ccc;
        }
        .enrolled-empty p { font-size: 17px; margin: 0 0 18px; }
        .enrolled-empty a {
            display: inline-block;
            padding: 10px 24px;
            background: var(--orange, #e67e22);
            color: #fff;
            border-radius: 8px;
            font-size: 15px;
            text-decoration: none;
            transition: background 0.2s;
        }
        .enrolled-empty a:hover { background: #cf6d17; }

        /* ── Dark Mode ── */
        body.dark .rec-card           { background: #1e1e2e; box-shadow: 0 4px 18px rgba(0,0,0,0.35); }
        body.dark .rec-card .card-title { color: #f0f0f0; }
        body.dark .rec-card .card-desc  { color: #aaa; }

        /* ── Responsive ── */
        @media (max-width: 600px) {
            .enrolled-section h1  { font-size: 22px; }
            .enrolled-grid        { grid-template-columns: 1fr; }
            .rec-card .card-btn-row { flex-direction: column; }
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
    <section class="enrolled-section">

        <div class="section-header">
            <i class="fas fa-bookmark"></i>
            <h1>My Enrolled Courses</h1>
        </div>
        <p class="section-sub">
            <?php echo count($enrolled_courses); ?>
            course<?php echo count($enrolled_courses) !== 1 ? 's' : ''; ?> enrolled
        </p>

        <div class="enrolled-grid">

            <?php if ($learner_id <= 0) : ?>
                <div class="enrolled-empty">
                    <i class="fas fa-user-slash"></i>
                    <p>Session error. Please log in again.</p>
                    <a href="../webpage/login.php">Go to Login</a>
                </div>

            <?php elseif (empty($enrolled_courses)) : ?>
                <div class="enrolled-empty">
                    <i class="fas fa-book-open"></i>
                    <p>You haven't enrolled in any courses yet.</p>
                    <a href="courses.php">Browse Courses</a>
                </div>

            <?php else : ?>

                <?php
                // Icon fallback map (same as courses.php)
                $icon_map = [
                    'web'      => 'fa-globe',
                    'python'   => 'fa-python',
                    'data'     => 'fa-chart-bar',
                    'machine'  => 'fa-robot',
                    'design'   => 'fa-paint-brush',
                    'mobile'   => 'fa-mobile-alt',
                    'database' => 'fa-database',
                    'security' => 'fa-shield-alt',
                    'cloud'    => 'fa-cloud',
                ];

                foreach ($enrolled_courses as $course) :
                    $cid      = (int) $course['id'];
                    $title    = htmlspecialchars($course['title']);
                    $category = htmlspecialchars($course['category']);
                    $desc     = htmlspecialchars($course['primary_description'] ?? '');
                    $image    = $course['image'];
                    $date     = date('M d, Y', strtotime($course['enrolled_at']));

                    $icon = 'fa-book';
                    foreach ($icon_map as $keyword => $fa) {
                        if (stripos($category, $keyword) !== false || stripos($title, $keyword) !== false) {
                            $icon = $fa;
                            break;
                        }
                    }
                ?>

                <div class="rec-card">

                    <!-- Thumbnail -->
                    <?php if (!empty($image)) : ?>
                        <img
                            src="../admin/<?php echo htmlspecialchars($course['image']); ?>"
                            alt="<?php echo $title; ?>"
                            class="card-thumb"
                            onerror="this.style.display='none';
                                     this.nextElementSibling.style.display='flex';">
                        <div class="card-thumb-placeholder" style="display:none;">
                            <i class="fas <?php echo $icon; ?>"></i>
                        </div>
                    <?php else : ?>
                        <div class="card-thumb-placeholder">
                            <i class="fas <?php echo $icon; ?>"></i>
                        </div>
                    <?php endif; ?>

                    <div class="card-body">
                        <span class="card-category"><?php echo $category; ?></span>
                        <p class="card-title"><?php echo $title; ?></p>

                        <?php if (!empty($desc)) : ?>
                            <p class="card-desc"><?php echo $desc; ?></p>
                        <?php endif; ?>

                        <!-- Enrollment date -->
                        <div class="enrolled-date">
                            <i class="fas fa-calendar-check"></i>
                            Enrolled on <?php echo $date; ?>
                        </div>

                        <!-- Two action buttons -->
                        <div class="card-btn-row">
                            <a href="course_detail.php?id=<?php echo $cid; ?>"
                               class="view-btn">
                                <i class="fas fa-play-circle"></i> View Course
                            </a>
                            <a href="certificate.php?course_id=<?php echo $cid; ?>"
                               class="cert-btn">
                                <i class="fas fa-certificate"></i> Certificate
                            </a>
                        </div>
                    </div>

                </div><!-- /.rec-card -->

                <?php endforeach; ?>
            <?php endif; ?>

        </div><!-- /.enrolled-grid -->
    </section>

    <!-- ─── FOOTER (DO NOT EDIT) ─────────────────────────────────────────── -->
    <footer class="footer">
        &copy; copyright @ 2024 ALL RIGHTS RESERVED. <span>EduGhar </span>For SKILLS!
    </footer>

    <!-- Global JS (navbar, sidebar, dark-mode — DO NOT EDIT) -->
    <script src="http://127.0.0.1:5500/Styles/script.js"></script>

</body>
</html>