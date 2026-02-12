<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "Mysql@123";
$dbname = "project6";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<?php
session_start();
if (!isset($_SESSION['firstname'])) {
    header("Location: login.php");
    exit;
}
$name = $_SESSION['firstname'];
$id   = $_SESSION['id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduGhar</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <!-- Your existing CSS (navbar, sidebar, footer, etc.) -->
    <link rel="stylesheet" href="http://127.0.0.1:5500/Styles/style.css">

    <style>
        /* ─── Root font fix: anchor rem to 10px if style.css shrinks it ── */
        .recommendation-section,
        .recommendation-section * {
            box-sizing: border-box;
        }

        /* ─── Recommendation Section ─────────────────────────────── */
        .recommendation-section {
            padding: 28px 24px 48px;
            max-width: 1200px;
            margin: 0 auto;
            font-size: 16px; /* explicit base so rem children are predictable */
        }

        .recommendation-section .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 6px;
        }

        .recommendation-section .section-header i {
            font-size: 26px;
            color: var(--orange, #e67e22);
        }

        .recommendation-section h1 {
            font-size: 28px;
            color: var(--black, #1a1a2e);
            font-weight: 700;
            margin: 0;
        }

        .recommendation-section .section-sub {
            font-size: 15px;
            color: var(--light-color, #888);
            margin-bottom: 28px;
            padding-left: 38px;
        }

        /* ─── Cards Grid ─────────────────────────────────────────── */
        .rec-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
        }

        /* ─── Individual Card ────────────────────────────────────── */
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

        /* Course thumbnail */
        .rec-card .card-thumb {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background: #f0f0f0;
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

        /* Card body */
        .rec-card .card-body {
            padding: 16px 18px 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

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

        .rec-card .card-title {
            font-size: 17px;
            font-weight: 700;
            color: var(--black, #1a1a2e);
            line-height: 1.4;
            margin: 0;
        }

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

        /* Match score badge */
        .rec-card .card-score {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #555;
            margin-top: 4px;
        }

        .rec-card .score-bar-wrap {
            flex: 1;
            height: 7px;
            background: #eee;
            border-radius: 50px;
            overflow: hidden;
        }

        .rec-card .score-bar {
            height: 100%;
            border-radius: 50px;
            background: linear-gradient(90deg, #e67e22, #f39c12);
        }

        .rec-card .score-label {
            font-size: 13px;
            font-weight: 700;
            color: var(--orange, #e67e22);
            white-space: nowrap;
        }

        /* ─── Empty / Error States ───────────────────────────────── */
        .rec-empty {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 16px;
            color: #888;
        }

        .rec-empty i {
            font-size: 52px;
            margin-bottom: 16px;
            display: block;
            color: #ccc;
        }

        .rec-empty p {
            font-size: 17px;
            margin: 0 0 18px;
        }

        .rec-empty a {
            display: inline-block;
            padding: 10px 24px;
            background: var(--orange, #e67e22);
            color: #fff;
            border-radius: 8px;
            font-size: 15px;
            text-decoration: none;
            transition: background 0.2s;
        }

        .rec-empty a:hover {
            background: #cf6d17;
        }

        /* ─── Dark mode support (if your CSS uses [data-theme="dark"]) */
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

        body.dark .rec-card .score-bar-wrap {
            background: #333;
        }

        /* ─── Responsive ─────────────────────────────────────────── */
        @media (max-width: 600px) {
            .recommendation-section h1 {
                font-size: 1.4rem;
            }

            .rec-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <!-- ─── HEADER (unchanged) ───────────────────────────────────── -->
    <header class="header">
        <section class="flex">
            <a href="home.php"><img src="http://127.0.0.1:5500/Styles/logo1.png" class="logo" alt="EduGhar"></a>
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

    <!-- ─── SIDEBAR (unchanged) ──────────────────────────────────── -->
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

    <!-- ─── MAIN CONTENT ─────────────────────────────────────────── -->
    <section class="recommendation-section">

        <div class="section-header">
            <i class="fas fa-magic"></i>
            <h1>Recommended For You</h1>
        </div>
        <p class="section-sub">Courses picked based on your interests</p>

        <div class="rec-grid">

            <?php
            $learner_id = $_SESSION['id'] ?? null;

            if (!$learner_id) {
                echo '<div class="rec-empty">
                        <i class="fas fa-user-slash"></i>
                        <p>Learner session not found. Please log in again.</p>
                        <a href="../webpage/login.php">Go to Login</a>
                      </div>';
            } else {
                // ── 1. Call Flask recommendation API ─────────────────
                $limit    = 6; // how many cards to show
                $url      = "http://127.0.0.1:5000/recommend/{$learner_id}?limit={$limit}";
                $response = @file_get_contents($url);

                if ($response === false) {
                    // Flask server is not running
                    echo '<div class="rec-empty">
                            <i class="fas fa-server"></i>
                            <p>Recommendation service is currently unavailable.</p>
                            <a href="courses.php">Browse All Courses</a>
                          </div>';
                } else {
                    $data = json_decode($response, true);

                    if (
                        isset($data['status']) &&
                        $data['status'] === 'success' &&
                        !empty($data['recommendations'])
                    ) {
                        // ── 2. Collect course IDs from API response ───
                        $rec_courses       = $data['recommendations'];
                        $course_ids        = array_column($rec_courses, 'id');
                        $similarity_map    = [];
                        foreach ($rec_courses as $r) {
                            $similarity_map[$r['id']] = $r['similarity_score'];
                        }

                        // ── 3. Fetch full course details from MySQL ───
                        $placeholders = implode(',', array_fill(0, count($course_ids), '?'));
                        $types        = str_repeat('i', count($course_ids));
                        $stmt         = $conn->prepare(
                            "SELECT id, title, category, primary_description, image
                             FROM course
                             WHERE id IN ($placeholders)"
                        );
                        $stmt->bind_param($types, ...$course_ids);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Index DB results by id so we can preserve API order
                        $db_courses = [];
                        while ($row = $result->fetch_assoc()) {
                            $db_courses[$row['id']] = $row;
                        }
                        $stmt->close();

                        // ── 4. Render cards in API-ranked order ───────
                        foreach ($course_ids as $cid) {
                            if (!isset($db_courses[$cid])) continue;

                            $course = $db_courses[$cid];
                            $score  = $similarity_map[$cid] ?? 0;
                            $pct    = round($score * 100); // 0-100

                            $title    = htmlspecialchars($course['title']);
                            $category = htmlspecialchars($course['category']);
                            $desc     = htmlspecialchars($course['primary_description'] ?? '');
                            $image    = $course['image'];

                            // Category → icon map for placeholder
                            $icon_map = [
                                'web'        => 'fa-globe',
                                'python'     => 'fa-python',
                                'data'       => 'fa-chart-bar',
                                'machine'    => 'fa-robot',
                                'design'     => 'fa-paint-brush',
                                'mobile'     => 'fa-mobile-alt',
                                'database'   => 'fa-database',
                                'security'   => 'fa-shield-alt',
                                'cloud'      => 'fa-cloud',
                            ];
                            $icon = 'fa-book';
                            foreach ($icon_map as $keyword => $fa) {
                                if (stripos($category, $keyword) !== false || stripos($title, $keyword) !== false) {
                                    $icon = $fa;
                                    break;
                                }
                            }
                            ?>

                            <div class="rec-card">

                                <?php if (!empty($image)): ?>
                                    <img
                                        src="<?php echo htmlspecialchars($image); ?>"
                                        alt="<?php echo $title; ?>"
                                        class="card-thumb"
                                        onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                    <div class="card-thumb-placeholder" style="display:none;">
                                        <i class="fas <?php echo $icon; ?>"></i>
                                    </div>
                                <?php else: ?>
                                    <div class="card-thumb-placeholder">
                                        <i class="fas <?php echo $icon; ?>"></i>
                                    </div>
                                <?php endif; ?>

                                <div class="card-body">
                                    <span class="card-category"><?php echo $category; ?></span>
                                    <p class="card-title"><?php echo $title; ?></p>

                                    <?php if (!empty($desc)): ?>
                                        <p class="card-desc"><?php echo $desc; ?></p>
                                    <?php endif; ?>

                                    <!-- Match score bar -->
                                    <div class="card-score">
                                        <span>Match</span>
                                        <div class="score-bar-wrap">
                                            <div class="score-bar" style="width:<?php echo $pct; ?>%;"></div>
                                        </div>
                                        <span class="score-label"><?php echo $pct; ?>%</span>
                                    </div>
                                </div>

                            </div>

                            <?php
                        }

                    } elseif (isset($data['status']) && $data['status'] === 'success' && empty($data['recommendations'])) {
                        // API worked but no matches found (interests not set up yet)
                        echo '<div class="rec-empty">
                                <i class="fas fa-compass"></i>
                                <p>We don\'t have enough info about your interests yet.</p>
                                <a href="profile.php">Set Your Interests</a>
                              </div>';
                    } else {
                        // API returned an error
                        $msg = htmlspecialchars($data['message'] ?? 'Unknown error from recommendation service.');
                        echo "<div class='rec-empty'>
                                <i class='fas fa-exclamation-circle'></i>
                                <p>{$msg}</p>
                                <a href='courses.php'>Browse All Courses</a>
                              </div>";
                    }
                }
            }

            $conn->close();
            ?>

        </div><!-- /.rec-grid -->
    </section>

    <!-- ─── FOOTER (unchanged) ───────────────────────────────────── -->
    <footer class="footer">
        &copy; copyright @ 2024 ALL RIGHTS RESERVED. <span>EduGhar </span>For SKILLS!
    </footer>

    <!-- Your existing JS (handles navbar, sidebar, dark-mode toggle) -->
    <script src="http://127.0.0.1:5500/Styles/script.js"></script>

</body>
</html>