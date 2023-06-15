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
			$course = "";
			if (isset($_POST["completed_courses"])) {
				$course = $_POST["completed_courses"];
			}

			$lines = explode("\n", $course);

			$table_key = [];
			$table_index = 0;
			$table_value = [];

			for ($i = 0; $i < count($lines); $i++) {
				if (trim($lines[$i]) == '學期成績紀錄') {
					$table_index = $i;
					$table_key = explode(' ', $lines[$i + 1]);
					break;
				}
			}

			for ($i = $table_index + 2; $i < count($lines) - 5; $i++) {
				$table_value[] = explode("\t", $lines[$i]);
			}

			echo count($table_value) . "\n";

			// 連接到 MySQL 資料庫
			$servername = "localhost";
			$username = "root";
			$password = "";
			$dbname = "ece";

			$conn = new mysqli($servername, $username, $password, $dbname);
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}

			// 創建表格
			$tableSql = "CREATE TABLE IF NOT EXISTS `my_course` (
				`rowid` INT AUTO_INCREMENT PRIMARY KEY,
				`semester` VARCHAR(4),
				`cos_id` VARCHAR(6),
				`dep_cname` VARCHAR(50),
				`cos_cname` VARCHAR(255),
				`cos_type` VARCHAR(20),
				`cos_credit` DECIMAL(4, 2),
				`grade` VARCHAR(8),
				`grade_status` VARCHAR(10),
				`teacher` VARCHAR(512),
				`brief` VARCHAR(65)
			)";

			if ($conn->query($tableSql) == TRUE) {
				echo "Table created successfully";
			} else {
				echo "Error creating table: " . $conn->error;
			}
			$truncateSql = "TRUNCATE TABLE `my_course`";
			$conn->query($truncateSql);

			// 將資料插入表格
			foreach ($table_value as $row) {
				if (count($row) === 11) {
					$semester = $row[1];
					$courseId = $row[2];
					$department = $row[3];
					$courseName = $row[4];
					$courseType = $row[5];
					$credits = $row[6];
					$grade = $row[7];
					$gradeStatus = $row[8];
					$instructor = $row[9];
					$dimension = $row[10];

					$insertSql = "INSERT INTO `my_course` (`semester`, `cos_id`, `dep_cname`, `cos_cname`, `cos_type`, `cos_credit`, `grade`, `grade_status`, `teacher`, `brief`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

					$stmt = $conn->prepare($insertSql);
					$stmt->bind_param("ssssssssss", $semester, $courseId, $department, $courseName, $courseType, $credits, $grade, $gradeStatus, $instructor, $dimension);

					if ($stmt->execute()) {
						echo " ";
					} else {
						echo "Error inserting record: " . $stmt->error;
					}
				} else {
					echo "Error: Invalid row format";
				}
			}

			// 關閉與資料庫的連接
			$conn->close();

			echo "資料已插入到 SQL 資料庫中！\n";
		?>


</body>
</html>