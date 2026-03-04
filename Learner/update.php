<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "Mysql@123";
$dbname = "project6";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check if user is logged in
if (!isset($_SESSION['firstname']) && !isset($_SESSION['id'])) {
   header("Location: login.php");
   exit;
}

$name = $_SESSION['firstname'];
$id = $_SESSION['id'];

$sql = "SELECT * FROM learnerlogin WHERE learner_id = $id";
$result = mysqli_query($conn,$sql);

if ($result->num_rows > 0) {
   $learner = $result->fetch_assoc();
} else {
   die("Learner not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>update</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="http://127.0.0.1:5500/Styles/style.css">

</head>
<body>

<header class="header">
   
   <section class="flex">

      <a href="home.php"><img src="http://127.0.0.1:5500/Styles/logo1.png" class="logo"></img></a>

      <!-- <form action="search.php" method="post" class="search-form">
         <input type="text" name="search_box" required placeholder="search courses..." maxlength="100">
         <button type="submit" class="fas fa-search"></button>
      </form> -->

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
         <!-- <a href="profile.php" class="btn">view profile</a> -->
         <div class="flex-btn">
            <a href="http://localhost/bCA-Project-II/styles/logout.php" class="option-btn">logout</a>
            <!-- <a href="register.php" class="option-btn">register</a> -->
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

<section class="form-container">

   <form action="loginUpdate.php" method="post">
      <h3>update profile</h3>
      <p>update firstname</p>
      <input type="text" name="fname" class="box" value="<?php echo $learner['firstname']; ?>">
      <p>update lastname</p>
      <input type="text" name="lname" class="box" value="<?php echo $learner['lastname']; ?>">
      <p>update email</p>
      <input type="email" name="email" class="box" value="<?php echo $learner['email']; ?>">
      <p>new password</p>
      <input type="password" name="new_pass" class="box" value="<?php echo $learner['password']; ?>">
      <p>Update Interests</p>
      <a class="btn" href="learnerinterest.php?learner_id=<?php echo $id; ?>">
         Update Interests
      </a>
      <input type="submit" value="update profile" name="submit" class="btn" style="background-color: #1a5490">
   </form>

</section>


<footer class="footer">

   &copy; copyright @ 2024 ALL RIGHTS RESERVED. <span>EduGhar </span>For SKILLS!

</footer>

<!-- custom js file link  -->
<script src="http://127.0.0.1:5500/Styles/script.js"></script>

   
</body>
</html>