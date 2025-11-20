<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>courses</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <header class="header">

        <section class="flex">

            <a href="admindashboard.php"><img src="logo1.png" class="logo"></img></a>

            <div class="icons">
                <div id="menu-btn" class="fas fa-bars"></div>
                <div id="search-btn" class="fas fa-search"></div>
                <div id="user-btn" class="fas fa-user"></div>
                <div id="toggle-btn" class="fas fa-sun"></div>
            </div>

            <div class="profile">
                <img src="images\pic-5.jpg" class="image" alt="">
                <h3 class="name"><?php // echo htmlspecialchars($name); 
                                    ?> Purnika Prajapati</h3>
                <p class="role">Admin</p>
                <!-- <a href="profile.html" class="btn">view profile</a> -->
                <div class="flex-btn">
                    <!-- <a href="login.html" class="option-btn">login</a> -->
                    <a href="logout.php" class="option-btn">Logout</a>
                </div>
            </div>

        </section>

    </header>

    <div class="side-bar">

        <div id="close-btn">
            <i class="fas fa-times"></i>
        </div>

        <div class="profile">
            <img src="images\pic-5.jpg" class="image" alt="">
            <h3 class="name"><?php //echo htmlspecialchars($name);?> Purnika Prajapati</h3>
            <p class="role">Admin</p>
        </div>

        <nav class="navbar">
            <a href="admindashboard.php"><i class="fas fa-home"></i><span>Dashboard</span></a>
            <a href="CRUD.php"><i class="fas fa-graduation-cap"></i><span>courses</span></a>
            <a href="learner-info.php"><i class="fas fa-chalkboard-user"></i><span>Learner-Info</span></a>
        </nav>

    </div>

    <div class="course-contents">
        <div class="course-card">
            <img src="images/courses/course-1.jpg">
            <div class="category">
                <div class="subject">
                    <h3>Design</h3>
                </div>
            </div>

            <h2 class="course-title">DBMS</h2>

            <div class="course-desc">
                <p>Duration : 3 months</p>
            </div>

            <div class="course-ratings">
                <button class="moreinfo">
                    <pre>More Information</pre>
                </button>
                <button class="get-started-btn btn">
                    <pre>   Start Learning   </pre>
                </button>
            </div>

        </div>
    </div>

        <footer class="footer">

            &copy; copyright @ 2024 ALL RIGHTS RESERVED. <span>EduGhar </span>For SKILLS!

        </footer>

</body>

</html>