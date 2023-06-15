<?php
session_start();
include "db_connect.php"
?>

<!DOCTYPE HTML PUBLIC >
<html>
<head >
<title> result </title >
<meta  http-equiv =" content-type " content =" text/html; charset=UTF-8 ">
</head >

<?php
        function  SaveChinese ( $string )
        {
                // $pattern = '/[\x{4E00}-\x{9FFF}]+/u'; // 使用Unicode 編碼範圍匹配繁體中文字符
                // preg_match_all($pattern, $string, $matches);
                // $filteredString = implode('', $matches[0]);
                // return $filteredString;
                $pattern = '/[\p{Han}\p{Latin}]+/u' ; // 匹配繁體中文和英文字母
                preg_match_all( $pattern , $string , $matches );
                $filteredString = implode( '' , $matches [ 0 ]);
                return  $filteredString ;
        }

        $tableSql = "CREATE TABLE IF NOT EXISTS `checktable` (
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
        if ( $conn -> query ( $tableSql ) == TRUE ) {
                echo " <script>alert('新資料表創建成功')</script> ";
        } else {
                echo " <script>alert('新資料表創建失敗')</script>: " . $conn -> error ;
        }
        $tableSql = " TRUNCATE TABLE `checktable` ";
        $conn -> query ( $tableSql );

        $sql = " INSERT INTO `checktable`
                SELECT * FROM `my_course` WHERE `grade_status`<> 'W' AND `grade`<> 'D' AND `grade`<> 'E' AND `grade`<> '' ";
        if ( $conn -> query ( $sql ) == TRUE ) {
                echo " <script>alert('插入成功')</script> ";
        } else {
                echo " <script>alert('插入失敗')</script> " . $conn -> error ;
        }
?>


<body>
        <style type="text/css">

                #sitebody {
                        width: 1200px;
                        margin: 0 auto;
                        font-size: 18px;
        
                }

                #sidebar_left {
                        background-color: rgba(221, 185, 220, 0.84);
                        width: 400px;
                        height: 1200px;
                        text-align: center;
                        float: left;
                }
                #sidebar_right {
                        background-color: rgba(221, 185, 220, 0.84);
                        width: 400px;
                        height: 1200px;
                        text-align: center;
                        float: right;
                }
                #content {
                        margin-left: 400px;
                        margin-right: 400px;
                        height: 1200px;
                        background-color:rgba(236, 207, 234, 0.84);
                        text-align: center;
                }

        </style>




        <div  id ="sitebody" >
                <div  id ="sidebar_left"> <br>
                         <h2>尚未修習必修</h2 >
                         <?php
                                $que = " SELECT * FROM `ece_course` WHERE `main_class` = '基礎必修課程' AND REGEXP_REPLACE(`cos_cname`, '[^[:alnum:]]', '') NOT IN (SELECT REGEXP_REPLACE(`cos_cname`, '[^[:alnum:]]', '') FROM `checktable`) ";
                                $course = mysqli_query( $conn , $que );
                                // while($meta = mysqli_fetch_field($course)){
                                // echo"$meta->name ";
                                // }
                                while ( $row = mysqli_fetch_row( $course )){
                                        echo " $row[0] <br> ";
                                }
                         ?>
                </div >
                <div  id ="sidebar_right"> <br>
                         <h2>其他未滿足條件</h2>
                         <?php
                         $que = " SELECT COUNT(*) FROM `checktable` WHERE `cos_cname`= '大一體育' ";
                         $ans = mysqli_query( $conn , $que );
                         $sum = mysqli_fetch_row( $ans );
                         if ( $sum [ 0 ] < 2 ){
                                $temp = 2 - $sum [ 0 ];
                                echo "大一體育差 $temp 門<br> ";
                         }

                         $que = " SELECT COUNT(*) FROM `checktable` WHERE `cos_type`= '體育' ";
                         $ans = mysqli_query( $conn , $que );
                         $sum = mysqli_fetch_row( $ans );
                         if ( $sum [ 0 ] < 8 ){
                                $temp = 6 - $sum [ 0 ];
                                if ( $temp > 0 ){
                                        echo "體育差 $temp 門<br> ";
                                }
                         }

                         echo "外語至少6學分，您已修";
                         $que = "SELECT IFNULL(SUM(`cos_credit`),0) FROM `checktable` WHERE `cos_type` = '外語'";
                         $ans = mysqli_query( $conn , $que );
                         $sum = mysqli_fetch_row( $ans );
                         echo " $sum[0]學分<br> ";

                         echo "專業選修至少33學分，您已修";
                         $que = " SELECT IFNULL(SUM(`cos_credit`),0) FROM `checktable` WHERE `cos_type`= '選修' AND `cos_cname` IN (SELECT `cos_cname` FROM `ece_course` WHERE `main_class` = '專業選修課程') ";
                         $ans = mysqli_query( $conn , $que );
                         $sum = mysqli_fetch_row( $ans );
                         echo " $sum[0]學分<br> ";

                         echo "校核心-基本素養至少6學分，您已修";
                         $que = " SELECT IFNULL(SUM(`cos_credit`),0) FROM `checktable` WHERE `brief` LIKE '基本素養%' ";
                         $ans = mysqli_query( $conn , $que );
                         $sum = mysqli_fetch_row( $ans );
                         echo " $sum[0]學分<br> ";
                        
                         echo "校核心-領域至少8學分，您已修";
                         $que = " SELECT IFNULL(SUM(`cos_credit`),0) FROM `checktable` WHERE `brief` LIKE '領域課程%' ";
                         $ans = mysqli_query( $conn , $que );
                         $sum = mysqli_fetch_row( $ans );
                         echo " $sum[0]學分<br> ";

                         echo "校核心課程至少18學分，您已修";
                         $que = " SELECT IFNULL(SUM(`cos_credit`),0) FROM `checktable` WHERE `brief` LIKE '領域課程%' OR `brief` LIKE '基本素養%' ";
                         $ans = mysqli_query( $conn , $que );
                         $sum = mysqli_fetch_row( $ans );
                         echo " $sum[0]學分<br> ";

                         echo "畢業至少128學分，您已修";
                         $que = " SELECT IFNULL(SUM(`cos_credit`),0) FROM `checktable` ";
                         $ans = mysqli_query( $conn , $que );
                         $sum = mysqli_fetch_row( $ans );
                         echo " $sum[0]學分<br> ";
                         ?>

                </div>
                <div id ="content" > <br>
                         <h2>建議選課</h2>   
                         <?php
                                echo " <h3>推薦選修:</h3> ";
                                $que = " SELECT * FROM `ece_course` WHERE (`main_class` = '專業選修課程' OR `main_class` = '專業必修實驗課程') AND REGEXP_REPLACE(`cos_cname`, '[^[:alnum:]]', '') NOT IN (SELECT REGEXP_REPLACE(`cos_cname`, '[^[:alnum:]]', '') FROM `checktable`) GROUP BY `cos_cname` ORDER BY RAND() LIMIT 10 ";
                                $course = mysqli_query( $conn , $que );
                                while ( $row = mysqli_fetch_row( $course )){
                                        echo " $row[0] <br> ";
                                }
                                echo " <h3>推薦核心課程:</h3> ";
                                $que = " SELECT * FROM 1112course WHERE (`brief` LIKE '領域課程%' OR `brief` LIKE '基本素養%') AND REGEXP_REPLACE(`cos_cname`, '[^[:alnum:]]', '') NOT IN (SELECT REGEXP_REPLACE(`cos_cname`, '[^[:alnum:]]', '') FROM `checktable`) GROUP BY `cos_cname` ORDER BY RAND() LIMIT 10 ";
                                $course = mysqli_query( $conn , $que );
                                while ( $row = mysqli_fetch_row( $course )){
                                        echo " $row[1] <br> ";
                                }
                         ?>    
                </div>

        </div>

        
</body>




</html> 