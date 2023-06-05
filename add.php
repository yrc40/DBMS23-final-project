<?php
session_start(); 
include "add_db_connect.php"
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
       <h2>Add Course</h2>
<div class=three> 
        <label>Course name</label>
        <br>
        <form action=add.php method="get" id="add">
        <input type="text" name="cos_cname" placeholder="Course name">
        <br>
        <label>Department name</label>
        <br>
        <input type="text" name="dep_cname" placeholder="Department name">
        <br>
        <label>Course type</label>
        <br>
        <input type="text" name="cos_type" placeholder="Course type">
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
$cos_cname=$_GET["cos_cname"];
$dep_cname=$_GET["dep_cname"];
$cos_type=$_GET["cos_type"];

 //sql語法存在變數中
$sql = "INSERT INTO  `my_course` (`cos_cname`,`dep_cname`, `cos_type`) VALUE ($cos_cname,$dep_cname,$cos_type) ";

// 用mysqli_query方法執行(sql語法)將結果存在變數中
$result = mysqli_query($conn,$sql);

function add_alert($message1, $message2, $message3) {
    echo "<script>alert('已輸入課程:$message1 開課單位:$message2 選別:$message3');</script>";
}
function nonadd_alert($message1, $message2, $message3) {
    echo "<script>alert('課程:$message1 開課單位:$message2 選別:$message3 已存在或不合法');</script>";
}
// 如果有異動到資料庫數量(更新資料庫)
if (mysqli_affected_rows($conn)>0) {
// 如果有一筆以上代表有更新
echo "成功新增未知課程";
add_alert($cos_cname,$dep_cname,$cos_type);
}
elseif(mysqli_affected_rows($conn)==0) {
    echo "無新增課程";
    add_alert($cos_cname,$dep_cname,$cos_type);
}
else {
    echo "{$sql} 語法執行失敗，錯誤訊息: " . mysqli_error($conn);
}
 mysqli_close($conn); 
?>

</body>
</html>
