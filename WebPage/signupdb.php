<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "Mysql@123";
$dbname = "project6";

$conn = mysqli_connect($servername,$username,$password,$dbname);

if (!$conn){
    die ("connection failed".mysqli_connect_error());
}

// echo "Connected Successfully <br>";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
$firstname = $_POST['fn'];
$lastname = $_POST['ln'];
$email = $_POST['e'];
$pass = $_POST['pass'];

if(empty($firstname) || empty($lastname) || empty($email) || empty($pass)){
    echo "All fields are required";
    exit;
}
else if(!preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9]{5}\.[a-zA-Z]{2,4}$/",$email)){
    echo "Enter a valid email";
    exit;
}
else if(strlen($pass)<6){
    echo "Password must be atleast 6 characters";
    exit;
}
else if(!preg_match("/^[a-zA-Z]*$/",$firstname) || !preg_match("/^[a-zA-Z]*$/",$lastname)){
    echo "Name must be in alphabets";
    exit;
}
else{
$sql= "INSERT INTO learnerlogin(firstname,lastname,email,password) values('$firstname','$lastname','$email','$pass')";

$result= mysqli_query($conn,$sql);
if($result){
    $learner_id = mysqli_insert_id($conn);
    header("Location:http://localhost/bca-Project-II/learner/learnerinterest.php?learner_id=$learner_id");
    exit;
}
else{
    echo mysqli_error($conn);
}
}

}
mysqli_close($conn);

?>