<?php
include('C:\xampp\htdocs\BCA-Project-II\db.php');

$sql = "SELECT * FROM courses_info";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html>

<head>
	<!--  *****   Link To Custom CSS Style Sheet   *****  -->
	<link rel="stylesheet" type="text/css" href="courseinfo.css">

	<!--  *****   Link To Font Awsome Icons   *****  -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

	<!--  *****   Link To Owl Carousel   *****  -->
	<link rel="stylesheet"
		href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />

	<link rel="stylesheet"
		href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" />

	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Course Info</title>
</head>

<body>
	<!--   *** Website Container Starts ***   -->
	<div class="website-container">

		<!--   *** Home Section Starts ***   -->
		<section class="home" id="home">
			<!--   === Main Navbar Starts ===   -->
			<nav class="main-navbar">
				<a href="#" class="logo">
					<img src="images\logo1.png" alt="Logo">
				</a>
				<ul class="nav-list">
					<li><a href="">Home</a></li>
					<li><a href="">About Us</a></li>
					<li><a href="">Courses</a></li>
					<li><a href="">Contact Us</a></li>
				</ul>
				<a href="#" class="get-started-btn-container">
					<button class="get-started-btn btn">Get Started</button>
				</a>
				<div class="menu-btn">
					<span></span>
				</div>
			</nav>
			<!--   === Main Navbar Ends ===   -->
		</section>

	</div>

	<div class="container">
		<?php
		echo '<section class="course-header">
				<h1>' . $row['title'] . '</h1>
			</section>

		<section class="course-info">
			<div class="info-content">
				
				
				<div class="text-section">
					<p>' . $row['main_description'] . '</p>

					<button class="start-btn">Start Learning</button>
				</div>

				<div class="image-section">
					<img src="images/courses/course-1.jpg" alt="Health and Social Care">
					<!--  Replace with your own image file and path -->
				</div>
			</div>
		</section>';
		?>

		<section class="learning-outcomes">
			<h2>What You Will Learn In This Free Course</h2>

			<?php
			$outcome_string = $row['learning_outcomes'];
			$outcome_array = explode(";", $outcome_string);
			foreach ($outcome_array as $outcome) {
				$clean_outcome = trim($outcome);
				if (!empty($clean_outcome)) {
					echo '<ul>
							<li>' . $clean_outcome . '</li>
						</ul>';
				}
			}
			?>
		</section>

		<section class="modules">
			<h2>Course Contents</h2>

			<?php
			$content_string = $row['course_content'];
			$content_array = explode(";", $content_string);
			foreach ($content_array as $content) {
				$clean_content = trim($content);
				if (!empty($clean_content)) {
					echo '
			<div class="module">
				<p>' . $clean_content . '</p>
			</div>';
				}
			}
			?>
		</section>


	</div>

	<!--   *** Link To JQuery ***   -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>

	<!--   *** Link To Owl Carousel ***   -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

	<!--   *** Link To Curstom Script File ***   -->
	<script type="text/javascript" src="script.js"></script>
</body>

</html>