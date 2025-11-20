<?php
include('C:\xampp\htdocs\BCA-Project-II\db.php');

$sql = "SELECT * FROM courses";
$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html>

<head>
	<!--  *****   Link To Custom CSS Style Sheet   *****  -->
	<link rel="stylesheet" type="text/css" href="style.css">

	<!--  *****   Link To Font Awsome Icons   *****  -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

	<!--  *****   Link To Owl Carousel   *****  -->
	<link rel="stylesheet"
		href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />

	<link rel="stylesheet"
		href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" />

	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title> Our Course</title>
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


			<!--   *** Courses Section Starts ***   -->
			<section class="courses" id="courses">
				<!--   *** Courses Header Starts ***   -->
				<header class="section-header">
					<div class="header-text">
						<h1>Choose Your Favourite Course</h1>
						<p>Explore free online courses with certificates.</p>

					</div>
					<button class="courses-btn btn">View All</button>
				</header>

				<div>
					<form action="" method="GET" class="search-container">
						<input type="text" name="search" value="<?php if (isset($_GET['search'])) {
																	echo $_GET['search'];
																} ?>" placeholder="What do you want to learn?" class="search-input" />
						<button class="search-btn" type="submit">
							<img src="images/search-icon.png" alt="Search" />
						</button>
					</form>
				</div>
				<!--   *** Courses Header Ends ***   -->
				<!--   *** Courses Contents Starts ***   -->
				<div class="course-contents">

					<?php
					if (isset($_GET['search'])) {
						$filtervalues = $_GET['search'];
						$query = "SELECT * FROM courses WHERE CONCAT(category,title) LIKE '%$filtervalues%' ";
						$query_run = mysqli_query($conn, $query);

						if (mysqli_num_rows($query_run) > 0) {
							foreach ($query_run as $items) {
								echo '<div class="course-card">';
								echo '<img src="images/courses/course-1.jpg">';
								echo '<div class="category">';
								echo '<div class="subject">';
								echo '<h3>' . $items['category'] . '</h3>';
								echo '</div>';
								echo '</div>';

								echo '<h2 class="course-title">' . $items['title'] . '</h2>';

								echo '<div class="course-desc">';
								echo '<p>Duration : ' . $items['duration'] . '</p>';
								echo '</div>';

								echo '<div class="course-ratings">';
								echo '<button class="moreinfo"><pre>More Information</pre></button>';
								echo '<button class="get-started-btn btn"><pre>   Start Learning   </pre></button>';
								echo '</div>';

								echo '</div>';
							}
						}
					} else {
						while ($row = mysqli_fetch_array($result)) {
							echo '<div class="course-card">';
							echo '<img src="images/courses/course-1.jpg">';
							echo '<div class="category">';
							echo '<div class="subject">';
							echo '<h3>' . $row['category'] . '</h3>';
							echo '</div>';
							echo '</div>';

							echo '<h2 class="course-title">' . $row['title'] . '</h2>';

							echo '<div class="course-desc">';
							echo '<p>Duration : ' . $row['duration'] . '</p>';
							echo '</div>';

							echo '<div class="course-ratings">';
							echo '<button class="moreinfo"><pre>More Information</pre></button>';
							echo '<button class="get-started-btn btn"><pre>   Start Learning   </pre></button>';
							echo '</div>';

							echo '</div>';
						}
					}

					?>

				</div>
				<!--   *** Courses Contents Ends ***   -->
			</section>
			<!--   *** Courses Section Ends ***   -->

	</div>
	<!--   *** Website Container Ends ***   -->



	<!--   *** Link To JQuery ***   -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>

	<!--   *** Link To Owl Carousel ***   -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

	<!--   *** Link To Curstom Script File ***   -->
	<script type="text/javascript" src="script.js"></script>
</body>

</html>