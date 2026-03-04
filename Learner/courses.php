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

$name = $_SESSION['firstname'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses EduGhar</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

    <!-- Your existing global CSS (navbar, sidebar, footer — DO NOT EDIT) -->
    <link rel="stylesheet" href="http://127.0.0.1:5500/Styles/style.css">

    <!-- ─── Internal CSS (same card system as home.php) ─────────────────── -->
    <style>

        /* ── Box-sizing reset scoped to this page's content ── */
        .courses-section,
        .courses-section * {
            box-sizing: border-box;
        }

        /* ── Page Section Wrapper ── */
        .courses-section {
            padding: 28px 24px 48px;
            max-width: 1200px;
            margin: 0 auto;
            font-size: 16px;
        }

        /* ── Section Header ── */
        .courses-section .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 6px;
        }

        .courses-section .section-header i {
            font-size: 26px;
            color: var(--orange, #e67e22);
        }

        .courses-section h1 {
            font-size: 28px;
            color: var(--black, #1a1a2e);
            font-weight: 700;
            margin: 0;
        }

        .courses-section .section-sub {
            font-size: 15px;
            color: var(--light-color, #888);
            margin-bottom: 28px;
            padding-left: 38px;
        }

        /* ── Cards Grid ── */
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
        }

        /* ── Individual Card ── */
        .rec-card {
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            display: flex;
            flex-direction: column;
        }

        .rec-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.13);
        }

        /* ── Thumbnail (real image from DB) ── */
        .rec-card .card-thumb {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background: #f0f0f0;
            display: block;
        }

        /* ── Placeholder shown when image is missing or broken ── */
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

        /* ── Course Title ── */
        .rec-card .card-title {
            font-size: 17px;
            font-weight: 700;
            color: var(--black, #1a1a2e);
            line-height: 1.4;
            margin: 0;
        }

        /* ── Short Description (3 lines max) ── */
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

        /* ── Enroll Now Button ── */
        .rec-card .enroll-btn {
            display: inline-block;
            margin-top: 4px;
            padding: 10px 0;
            width: 100%;
            text-align: center;
            background: var(--orange, #e67e22);
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            letter-spacing: 0.03em;
            transition: background 0.2s ease, transform 0.15s ease;
        }

        .rec-card .enroll-btn:hover {
            background: #cf6d17;
            transform: scale(1.02);
        }

        /* ── Empty / Error State ── */
        .courses-empty {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 16px;
            color: #888;
        }

        .courses-empty i {
            font-size: 52px;
            margin-bottom: 16px;
            display: block;
            color: #ccc;
        }

        .courses-empty p {
            font-size: 17px;
            margin: 0;
        }

        /* ── Dark Mode Support ── */
        body.dark .rec-card {
            background: #1e1e2e;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.35);
        }

        body.dark .rec-card .card-title {
            color: #f0f0f0;
        }

        body.dark .rec-card .card-desc {
            color: #aaa;
        }

        body.dark .rec-card .enroll-btn {
            background: #e67e22;
        }

        body.dark .rec-card .enroll-btn:hover {
            background: #cf6d17;
        }

        /* ── Responsive ── */
        @media (max-width: 600px) {
            .courses-section h1 {
                font-size: 22px;
            }

            .courses-grid {
                grid-template-columns: 1fr;
            }
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
                <div id="menu-btn" class="fas fa-bars"></div>
                <div id="search-btn" class="fas fa-search"></div>
                <div id="user-btn" class="fas fa-user"></div>
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
        <div id="close-btn">
            <i class="fas fa-times"></i>
        </div>

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
    <section class="courses-section">

        <div class="section-header">
            <i class="fas fa-graduation-cap"></i>
            <h1>All Courses</h1>
        </div>
        <p class="section-sub">Browse and enroll in any course available</p>

        <div class="courses-grid">

            <?php
            // ── Fetch all courses from DB ─────────────────────────────────
            $sql    = "SELECT id, title, category, primary_description, image FROM course ORDER BY id ASC";
            $result = $conn->query($sql);

            if (!$result) {
                // Query failed
                echo '<div class="courses-empty">
                        <i class="fas fa-exclamation-circle"></i>
                        <p>Could not load courses. Please try again later.</p>
                      </div>';
            } elseif ($result->num_rows === 0) {
                // No courses in the table yet
                echo '<div class="courses-empty">
                        <i class="fas fa-book-open"></i>
                        <p>No courses available at the moment. Check back soon!</p>
                      </div>';
            } else {
                // ── Icon fallback map (same as home.php) ─────────────────
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

                while ($course = $result->fetch_assoc()) {
                    $id       = (int) $course['id'];
                    $title    = htmlspecialchars($course['title']);
                    $category = htmlspecialchars($course['category']);
                    $desc     = htmlspecialchars($course['primary_description'] ?? '');
                    $image    = $course['image'];

                    // Pick icon for placeholder
                    $icon = 'fa-book';
                    foreach ($icon_map as $keyword => $fa) {
                        if (
                            stripos($category, $keyword) !== false ||
                            stripos($title,    $keyword) !== false
                        ) {
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

                        <!-- Card Body -->
                        <div class="card-body">
                            <span class="card-category"><?php echo $category; ?></span>
                            <p class="card-title"><?php echo $title; ?></p>

                            <?php if (!empty($desc)) : ?>
                                <p class="card-desc"><?php echo $desc; ?></p>
                            <?php endif; ?>

                            <!-- Enroll Now Button → links to course_detail.php with course id -->
                            <a href="course_detail.php?id=<?php echo $id; ?>" class="enroll-btn">
                                Enroll Now &nbsp;<i class="fas fa-arrow-right"></i>
                            </a>
                        </div>

                    </div>

                    <?php
                } // end while
            } // end else

            $conn->close();
            ?>

        </div><!-- /.courses-grid -->
    </section>

    <!-- ─── FOOTER (DO NOT EDIT) ─────────────────────────────────────────── -->
    <footer class="footer">
        &copy; copyright @ 2024 ALL RIGHTS RESERVED. <span>EduGhar </span>For SKILLS!
    </footer>

    <!-- Your existing JS (handles navbar, sidebar, dark-mode toggle — DO NOT EDIT) -->
    <script src="http://127.0.0.1:5500/Styles/script.js"></script>

</body>
</html>