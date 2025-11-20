<html>
<head>
    <title>Display Page</title>
</head>

<body>
    <h1>Displaying Files uploaded in Database</h1>
    <div>
        <?php
        include('db.php');

        $sql = "SELECT * FROM insertfile";
        $result = mysqli_query($conn, $sql);

        while($row = mysqli_fetch_array($result)){
           $name = $row['filepath']; 
        ?>
              <!-- displaying video and skipping non-video files -->
           <?php if (strpos($row['filepath'], '.mp4') !== false) { ?>
               <video width="1000" height="550" controls>
                   <source src="<?php echo 'uploads/'. $row['filepath']; ?>" type="video/mp4">
               </video>
           <?php } ?>
            <br><br>
           <!-- img diplaying and skipping video files -->
           <?php if (strpos($row['filepath'], '.mp4') === false) { ?>
               <img src="<?php echo 'uploads/'. $row['filepath']; ?>" width="300" height="200"/><br><br>
           <?php } ?>

        <?php
        }
        ?>
    </div>
</body>

</html>