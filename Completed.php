<?php
session_start(); 
include "db_connect.php"
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses completed</title>
    <link rel="stylesheet" href="Completed.css">
</head>
<body>
   
<div class =three>
        <label>Courses completed</label>
        <br>
        <form action="Completed.php" id="completed" method="post">
            <textarea name="completed_courses"
                    style="font-size: 16px"
                    rows="18"
                    cols="20"
                    placeholder="Courses completed"
                    required></textarea>
        </form>
        <br>
        <br>
        <button type="submit" form="completed">Submit</button>
        <br>
        <br>
        <form action="index.php" id="index">
        </form>
        <button type="submit" form="index">Home</button>
        </div>

        <?php
            $course="";
            if(isset($_POST["completed_courses"])){
                $course=$_POST["completed_courses"];
            }
            $course=explode("\n",$course);
            for ($x=0; $x<count($course); $x++) {
	            $acourse=preg_split("/\s+/", $course[$x]);
                if(count($acourse)==9){
                    $sql="INSERT INTO `my_course` (`rowid`, `cos_id`, `semester`, `dep_cname`, `cos_cname`, `cos_type`, `cos_credit`) 
                        VALUES ('$acourse[0]','$acourse[1]','$acourse[2]','$acourse[3]','$acourse[4]','$acourse[5]','$acourse[6]')";
                    if (mysqli_query($conn, $sql)) {
                        echo "New record created successfully";
                      } else {
                        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                      }
                }
                else{
                    $sql="INSERT INTO `my_course` (`rowid`, `cos_id`, `semester`, `dep_cname`, `cos_cname`, `cos_type`, `cos_credit`, `grade`, `grade_status`, `teacher`, `brief`) 
                    VALUES ('$acourse[0]','$acourse[1]','$acourse[2]','$acourse[3]','$acourse[4]','$acourse[5]','$acourse[6]','$acourse[7]','$acourse[8]','$acourse[9]','$acourse[10]') ";
                    if (mysqli_query($conn, $sql)) {
                        echo "New record created successfully";
                      } else {
                        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                      }
                }    
            }
            mysqli_close($conn);
        ?>
</body>
</html>
