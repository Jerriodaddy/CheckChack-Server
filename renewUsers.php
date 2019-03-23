<?php
$servername = "127.0.0.1";
$username = "root";
$password = "root";
$database = "CheckChack";
// 创建连接
$conn = new mysqli($servername, $username, $password, $database);
 
// 检测连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
} 

$del_res = mysqli_query( $conn, "DROP TABLE Users" );
if(!$del_res )
{
  die('数据表删除失败: ' . mysqli_error($conn));
}
echo "数据表删除成功\n";
// 创建Seats Table.
//cur_state: 0=offline 1=reserve 2=seating
//gender: 0=female, 1=male, 2 =others
//year: 0=Y1 1=Y2 2=Y3 3=Y4 4=Graduate
$creat = "CREATE TABLE IF NOT EXISTS `Users` (
		`unionId` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT 'NULL',
		`openId` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
		`name` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT 'NULL',
		`studentId` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT 'NULL',
		`gender` tinyint(1) NOT NULL DEFAULT 2,
		`phone` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT 'NULL',
		`year` tinyint(1) NOT NULL DEFAULT 0,
		`major` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT 'NULL',
		`motto` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT 'NULL',
		`cur_state` tinyint(1) NOT NULL DEFAULT 0,
		`cur_seat` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT 'NULL',
		`create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`last_visit_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`usage_count` int NOT NULL DEFAULT 0,
		`studytime` int DEFAULT 0,
		PRIMARY KEY (`openId`),
		KEY `openId` (`openId`) USING BTREE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='UsersInfo';";
$creat_res = mysqli_query($conn, $creat);
if (!$creat_res) {
	die('数据表建立失败: ' . mysqli_error($conn));
}
echo "数据表建立成功\n";
// 插入测试数据
$sql = "INSERT INTO Users (openId, cur_state) 
		VALUES ('test', 0)";

if ($conn->query($sql) === TRUE) {
    echo "新记录插入成功\n";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// mysqli_query($conn, "UPDATE Seats SET loca_x=15, loca_y=87 WHERE seat_id='L405001';");


$conn->close();
?>