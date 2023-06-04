<?php

$sname= "localhost";
$unmae= "root";
$password = "";
$db_name = "";//在""中填入資料庫名稱

$conn = mysqli_connect($sname, $unmae, $password, $db_name);

if (!$conn) {
	echo "Connection failed!";
}
?>
