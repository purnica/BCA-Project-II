<?php

session_start();

// Check if user is logged in
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
   <title>contact us</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="http://127.0.0.1:5500/Styles/style.css">

</head>
<body>

<header class="header">
   
   <section class="flex">

      <a href="home.php"><img src="http://127.0.0.1:5500/Styles/logo1.png" class="logo"></img></a>


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
            <!-- <a href="register.html" class="option-btn">register</a> -->
         </div>
      </div>

   </section>

</header>   

<div class="side-bar">

   <div id="close-btn">
      <i class="fas fa-times"></i>
   </div>

   <div class="profile">
      <img src="http://127.0.0.1:5500/Styles/pic-5.jpg" class="image" alt="">
      <h3 class="name"><?php echo htmlspecialchars($name); ?></h3>
      <p class="role">student</p>
      <!-- <a href="profile.php" class="btn">view profile</a> -->
   </div>

   <nav class="navbar">
      <a href="home.php"><i class="fas fa-home"></i><span>home</span></a>
      <a href="about.php"><i class="fas fa-question"></i><span>about</span></a>
      <a href="courses.php"><i class="fas fa-graduation-cap"></i><span>courses</span></a>
      <a href="EnrolledCourses.php"><i class="fas fa-bookmark"></i><span>my courses</span></a>
      <a href="contact.php"><i class="fas fa-headset"></i><span>contact us</span></a>
   </nav>

</div>

<section class="contact">

   <div class="row">

      <div class="image">
         <img src="http://127.0.0.1:5500/Styles/contactus-hero.svg" alt="">
      </div>

   </div>

   <div class="box-container">

      <div class="box">
         <i class="fas fa-phone"></i>
         <h3>phone number</h3>
         <a href="tel:1234567890">+977- 9841000321</a>
         <a href="tel:1112223333">977- 9841000322</a>
      </div>
      
      <div class="box">
         <i class="fas fa-envelope"></i>
         <h3>email address</h3>
         <a href="mailto:shaikhanas@gmail.com">edughar@gmail.com.np</a>
         <a href="mailto:anasbhai@gmail.com">purnikaupasana@gmail.com</a>
      </div>

      <div class="box">
         <i class="fas fa-map-marker-alt"></i>
         <h3>office address</h3>
         <a href="#">United College, Kumaripati, Lalitpur Street no. 9999</a>
      </div>

   </div>

</section>

<footer class="footer">

   &copy; copyright @ 2024 ALL RIGHTS RESERVED. <span>EduGhar </span>For SKILLS!

</footer>

<!-- custom js file link  -->
<script src="http://127.0.0.1:5500/Styles/script.js"></script>

   
</body>
</html>