<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home Page</title>
    <link rel="stylesheet" href="WebPage.css" />
  </head>

  <body>

    <!-- ................. NavBar ......................... -->
    <nav class="navbar">
      <!-- logo -->
      <div class="logo">
        <a href="#container"><img src="logo1.png" alt="EduGhar Logo" /></a>
      </div>
      <!-- links -->
      <ul class="nav-links">
        <li><a href="#container">Home</a></li>
        <li><a href="#aboutcontainer">About</a></li>
        <li><a href="http://localhost/bCA-Project-II/courses/courses.php">Courses</a></li>
        <li><a href="#footer">Contact Us</a></li>
      </ul>
      <!-- search bar -->
      <!-- <div class="search-container">
        <input type="text" placeholder="Search..." class="search-input" />
        <button class="search-btn">
          <img src="img/search-icon.png" alt="Search" />
        </button>
      </div> -->
      <!-- login / signup button -->
      <div class="nav-icons">
        <a href="login.php" class="login-button">Login</a>
        <a href="signup.php" class="sign-button">Sign up</a>
      </div>
    </nav>

    
    <!--...................... HeroPage ....................... -->
    <div id="container">
      <!-- left side -->
      <div class="left-container">
        <h1><span class="highlight">Learn</span> on your</h1>
        <h1 class="lower">
          schedule. <img src="img\time.png" alt="time" class="time" width="55px" height="55px">
        </h1>
        <p>Anywhere, Anytime, Start learning today!</p>
        

        <a href="http://localhost/BCA-Project-II/webpage/login.php" class="get-started">Get Started</a>
      </div>

      <!-- right side -->
      <div class="right-container">
        <img src="img/Creatives1.png" alt="picture" class="hero-image" width="700px" height="700px">
      </div>

    </div>
    <!--...................... HeroPage Close....................... -->


  <!-- ..................... About Us................................. -->
  <div id="aboutcontainer">
    <!-- left side -->
    <div class="bodyleft">
      <img src="img/about.jpg" alt="pic" class="about-image" width="600px" height="500px">
    </div>

    <!-- right side -->
     <div class="bodyright">
      <img src="img/ABOUT PAGE.jpg" alt="pic" class="about-text" width="500px" height="450px">
     </div> 
  </div>
  <!-- ........................AboutUs Close............................ -->



<!-- ........................ Course Page ............................ -->
<section class="courses" id="courses"> 
  </div> 
  <h1 id="heading">Our Courses</h1>
  <div class="swiper course-slider">
    <div class="swiper-wrapper">

      <div class="swiper-slide slide">
        <img src="img/HTML5.png"   alt="">
        <h3>HTML</h3>
        <p>The language for building web pages.</p>
        <!-- <button type="button" class="btn">HTML</button> -->
      </div>

      <div class="swiper-slide slide">
        <img src="img/javalogo.jpg"  alt="">
        <h3>JAVA</h3>
        <p>A programming language</p>
      </div>

      <div class="swiper-slide slide">
        <img src="img/css.png" alt="">
        <h3>CSS:</h3>
        <p>The language for styling web pages.</p>
      </div>

      <div class="swiper-slide slide">
        <img src="img/dm.png" alt="">
        <h3>Digital Marketing</h3>
        <p>The strategy for promoting brands online.</p>
      </div>

      <div class="swiper-slide slide">
        <img src="img/figma.png" alt="">
        <h3>Figma</h3>
        <p>The platform for designing user interfaces collaboratively.</p>
      </div>
    </div>
    <!-- <div class="swiper-pagination"></div> -->
  </div>
</section>
<!-- swiper js link -->
<!-- <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script> -->
<!-- <script src="swiper.js"></script> -->
<!-- ......................CoursePage Close................................ -->



<!-- ..................... Footer ........................... -->
<footer id="footer">
  <div class="footer-content">
      <div class="footer-left">
          <img src="logo1.png" alt="Logo" class="logo"><br><br>
          <b class="strong">Contact With Us</b>
          <p class="contact">Reach us at <u>edughar@gmail.com.np</u> <br>
            or, <br>
            +977- 9841000288 <br>
            +977- 9841000289 <br>
            United College <br>
            Kumaripati, Lalitpur</p>
          <p class="rights_reserved">© 2024 ALL RIGHTS RESERVED. EduGhar For SKILLS</p>
      </div>
      <div class="footer-right">
          <b class="strong"><u>Links</u></b>
          <a href="#container">HOME</a>
          <a href="#aboutcontainer">ABOUT</a>
          <a href="#heading">COURSES</a>
          <a href="#footer">CONTACT US</a>
      </div>
  </div>
</footer>

<!-- ..................... Footer End ........................ -->

  </body>
</html>
