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
    <title>Add</title>
    <link rel="stylesheet" href="add.css">
</head>

<body>
       <h2>Add Course(加入抵免課程)</h2>
<div class=three> 
        <label>Course name</label>
        <br>
        <form action=add.php method="get" id="add">
        <input type="text" name="cos_cname" placeholder="Course name" required="required">
        <br>
        <label>Course credit</label>
        <br>
        <!--restrict input value to handle exception-->
        <select name="cos_credit" required="required">
            <option>Course credit</option>
            <option>0</option>
            <option>1</option>
            <option>2</option>
            <option>3</option>
            <option>4</option>
            <option>5</option>
            <option>6</option>
        </select>
        <br>
        <br>
        <label>Course type</label>
        <br>
        <!--restrict input value to handle exception-->
        <select name="cos_type" required="required">
            <option>Course type</option>
            <option>必修</option>
            <option>選修</option>
            <option>外語</option>
            <option>通識</option>
            <option>體育</option>
        </select>
        <br>
        <br>
        </form>
        <button type="submit" form="add">Submit</button>
        <br>
        <br>
        <form action="index.php" id="index">
        </form>
        <button type="submit" form="index" >Home</button>
        <br>
</div>

<?php

function SaveChinese($string){
    $pattern = '/[\p{Han}\p{Latin}]+/u'; 
    preg_match_all($pattern, $string, $matches);
    $filteredString = implode('', $matches[0]);
    return $filteredString;
}

//define the variables
$cos_cname = $_GET["cos_cname"];
$cos_credit = $_GET["cos_credit"];
$cos_credit = (int)$cos_credit;
$cos_type = $_GET["cos_type"];
$liberal1101 = preg_match("/\領域課程/i", $cos_cname); //boolean
$liberal1102 = preg_match("/\基本素養/i", $cos_cname); //boolean
$PE1 = preg_match("/\體育/i", $cos_cname); //boolean
$PE2 = preg_match("/\大一體育/i", $cos_cname); //boolean
$bug = false;//boolean

//insertion information
function cos_alert($message1, $message2, $message3) {
    echo "<script>alert('已輸入抵免課程:$message1 學分:$message2 選別:$message3');</script>";
}
function liberal_alert($message1, $message2) {
    echo "<script>alert('已輸入通識抵免 學分:$message1 向度:$message2');</script>";
}

//general debug
function debug($conn, $sql){
    if (mysqli_query($conn, $sql)) {
        echo "query successfully" . "<br>";
    } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

//calculate rowid of new data
$sql = "SELECT COUNT(*) FROM `my_course`";
$result = $conn->query($sql);
$rowid = mysqli_fetch_array($result)[0]+1;

//input debug
switch($cos_type) {
case "通識":
    if(!$liberal1101 && !$liberal1102) {
        echo "<script>alert('輸入的通識抵免向度可能不存在！請再輸入一次');</script>";
        $bug = true;
    } 
    break;
case "體育":
    if($cos_credit!=0) {
        echo "<script>alert('體育課程只能是0學分！請再輸入一次');</script>";
        $bug = true;
    } /*elseif(!$PE1 && !$PE2) {
        echo "<script>alert('體育課程名稱可能有誤!請再輸入一次');</script>";
        $bug = true;
    }*/
    break;
case "外語":
    if($cos_credit==0) {
        echo "<script>alert('外語課程不能是0學分！請再輸入一次');</script>";
        $bug = true;
    } 
    break;
case "必修":
    $cos_name = SaveChinese($cos_cname);
    //echo $cos_name;
    $sql = "SELECT * FROM `ece_course` WHERE (`main_class` = '基礎必修課程' OR `main_class` = '專業必修實驗課程') AND REGEXP_REPLACE(`cos_cname`, '[^[:alnum:]]', '') = '$cos_name' ";
    $result = $conn->query($sql);
    $nums=mysqli_num_rows($result);
    debug($conn, $sql);
    if($nums==0) { 
        echo "<script>alert('輸入之必修課程名稱可能有誤！請再輸入一次');</script>";
        $bug = true;
    } else { //check credit
        $sql = "SELECT `cos_credit` FROM (SELECT * FROM `1111course` UNION SELECT * FROM `1112course`) AS `111` WHERE REGEXP_REPLACE(`cos_cname`, '[^[:alnum:]]', '') = '$cos_name' AND `cos_type` = '必修'";
        debug($conn, $sql);
        $result = mysqli_query($conn, $sql);
        $credit = mysqli_fetch_array($result)[0];
        if($credit != $cos_credit){
            echo "<script>alert('必修課程$cos_cname 應為 $credit 學分！請再輸入一次');</script>";
            $bug = true;
        } 
    }
    break;
}

if(!$bug){
    if($cos_type == "通識") {
        $sql = "INSERT INTO `my_course` (`rowid`, `cos_cname`,`cos_credit`, `cos_type`, `grade`, `grade_status`, `brief`) VALUES ('$rowid','$cos_cname','$cos_credit','$cos_type', '同意抵免', '已送註冊組', '$cos_cname') ";
        if (mysqli_query($conn, $sql)) {
            echo "insert successfully";
            liberal_alert($cos_credit, $cos_cname);
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
              }
    } elseif($cos_type == "體育"){
        $sql = "INSERT INTO `my_course` (`rowid`, `cos_cname`,`cos_credit`, `cos_type`, `grade`, `grade_status`,`brief`) VALUES ('$rowid','$cos_cname','$cos_credit','$cos_type', '同意抵免', '已送註冊組', '體育必修') ";
        if (mysqli_query($conn, $sql)) {
            echo "insert successfully";
            cos_alert($cos_cname,$cos_credit, $cos_type);
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
              }
    } elseif($cos_cname != "") {
        $sql = "INSERT INTO `my_course` (`rowid`, `cos_cname`,`cos_credit`, `grade`, `grade_status`, `cos_type`) VALUES ('$rowid','$cos_cname','$cos_credit', '同意抵免', '已送註冊組', '$cos_type') ";
        if (mysqli_query($conn, $sql)) {
             echo "insert successfully";
             cos_alert($cos_cname,$cos_credit, $cos_type);
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
}

 mysqli_close($conn); 
?>

</body>
</html>
