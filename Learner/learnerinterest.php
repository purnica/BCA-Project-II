
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Favourite Courses</title>
<link rel="stylesheet" href="style\learnerinterest.css">
</head>
<body>
<!-- if there is id in the learnerinterest.php Url then only passing learner ID in interestdb.php -->
<?php
if(isset($_GET['learner_id'])){
  $learner_id = $_GET['learner_id'];
} else {
  //not passing id in the interestdb.php URL
  $learner_id = "";
}
?>

<form action="http://localhost/bca-Project-II/learner/interestdb.php?learner_id=<?php echo $learner_id; ?>" method="POST" class="container">
  <h2>Pick your interests:</h2>

  <div class="options">
    <label class="pill">
      <input type="checkbox" name="courses[]" value="python">
      Python
    </label>

    <label class="pill">
      <input type="checkbox" name="courses[]" value="machine learning">
      Machine Learning
    </label>

    <label class="pill">
      <input type="checkbox" name="courses[]" value="data science">
        Data Science
    </label>

    <label class="pill">
      <input type="checkbox" name="courses[]" value="web development">
        Web Development
    </label>

    <label class="pill">
      <input type="checkbox" name="courses[]" value="Artificial Intelligence">
        Artificial Intelligence
    </label>

    <label class="pill">
      <input type="checkbox" name="courses[]" value="Cloud Computing">
        Cloud Computing
    </label>
     <label class="pill">
      <input type="checkbox" name="courses[]" value="Computer Networks">
      Computer Networks
    </label>

    <label class="pill">
      <input type="checkbox" name="courses[]" value="Design">
      Design
    </label>

    <label class="pill">
      <input type="checkbox" name="courses[]" value="Cyber Security">
        Cyber Security
    </label>

    <label class="pill">
      <input type="checkbox" name="courses[]" value="Mobile App Development">
        Mobile App Development
    </label>

    <label class="pill">
      <input type="checkbox" name="courses[]" value="UI/UX Design">
        UI/UX Design
    </label>

    <label class="pill">
      <input type="checkbox" name="courses[]" value="Data Analytics">
        Data Analytics
    </label>

     <label class="pill">
      <input type="checkbox" name="courses[]" value="Arts">
      Arts
    </label>

    <label class="pill">
      <input type="checkbox" name="courses[]" value="Clothing Designing">
        Clothing Designing
    </label>

    <label class="pill">
      <input type="checkbox" name="courses[]" value="Banking">
        Banking
    </label>

    <label class="pill">
      <input type="checkbox" name="courses[]" value="Marketing">
        Marketing
    </label>

    <label class="pill">
      <input type="checkbox" name="courses[]" value="Finance">
        Finance
    </label>

    <label class="pill">
      <input type="checkbox" name="courses[]" value="Personality Development">
        Personality Development
    </label>

    <label class="pill">
      <input type="checkbox" name="courses[]" value="Photography">
        Photography
    </label>

    <label class="pill">
      <input type="checkbox" name="courses[]" value="Leadership Skills">
        Leadership Skills
    </label>

    <label class="pill">
      <input type="checkbox" name="courses[]" value="Communication Skills">
        Communication Skills
    </label>

    <label class="pill">
      <input type="checkbox" name="courses[]" value="Physiotherapy">
        Physiotherapy
    </label>

    <label class="pill">
      <input type="checkbox" name="courses[]" value="Phycology">
        Phycology
    </label>

    <label class="pill">
      <input type="checkbox" name="courses[]" value="Time Management">
        Time Management
    </label>

    <label class="pill">
      <input type="checkbox" name="courses[]" value="Project Management">
        Project Management
    </label>

    <label class="pill">
      <input type="checkbox" name="courses[]" value="Digital Marketing">
        Digital Marketing
    </label>

     <label class="pill">
      <input type="checkbox" name="courses[]" value="Skills Development">
        Skills Development
    </label>

     <label class="pill">
      <input type="checkbox" name="courses[]" value="Other">
        Other
    </label>
    
  </div>

  <button type="submit">Submit</button>
</form>

</body>
</html>
