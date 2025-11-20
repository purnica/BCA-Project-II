<?php
include('db.php');

if (isset($_POST['upload'])) {
    $name = $_FILES['fileUpload'];

    //printing the file information array
    // print_r($name);
    // exit();

    //storing file information in variables
    $filename = $_FILES['fileUpload']['name'];
    $tempname = $_FILES['fileUpload']['tmp_name'];
    $filetype = $_FILES['fileUpload']['type'];
    $filesize = $_FILES['fileUpload']['size'];
    $filedestination = "uploads/" . $filename; //uploading the file in the folder.

    if (move_uploaded_file($tempname, $filedestination)) {
        $insert = "INSERT INTO insertfile(filepath) VALUES('$filename')";

        if (mysqli_query($conn, $insert)) {
            $success= "File uploaded successfully";
        } else {
            $failed= "Database insertion failed.";
        }
    } else {
        $msg= "Failed to upload file.";
    }
}
?>


<html>
<head>
    <title>Video Page</title>
</head>

<body>
    <h1>Upload Video or Image file</h1>
    <div>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            Select video or image to upload:
            <input type="file" name="fileUpload" id="fileToUpload"><br><br>
            <?php 
            if (isset($success)) {
                echo "<h3 style='color:green;'>$success</h3>";
            }
            if (isset($failed)) {
                echo "<h3 style='color:red;'>$failed</h3>";
            }
            if (isset($msg)) {
                echo "<h3 style='color:red;'>$msg</h3>";
            }
            ?>
            <br><br>
            <input type="submit" value="Upload File" name="upload">
        </form>
    </div>
</body>

</html>