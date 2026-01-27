<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "Mysql@123";
$dbname = "project";

$conn = new mysqli($servername, $username, $password, $dbname);

$id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $pass = $_POST['new_pass'];

    if(empty($fname) || empty($lname) || empty($email) || empty($pass)){
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
    else if(!preg_match("/^[a-zA-Z]*$/",$fname) || !preg_match("/^[a-zA-Z]*$/",$lname)){
        echo "Name must be in alphabets";
        exit;
    }
    else{
    $sql = "UPDATE signup SET firstname = '$fname', lastname = '$lname', email = '$email', password = '$pass' WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        $_SESSION['firstname'] = $fname;
        $_SESSION['lastname'] = $lname;
        echo '<script>
                alert("Profile Updated successfully!");
                window.location.href = "profile.php";
             </script>';
    } else {
        echo "Error updating course: " . $conn->error;
    }
}
$conn->close();
}
?>