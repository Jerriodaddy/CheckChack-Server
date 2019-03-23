<?php
$servername = "127.0.0.1";
$username = "root";
$password = "root";
$database = "CheckChack";
// 创建连接
$conn = new mysqli($servername, $username, $password, $database);
mysqli_query($conn, "set names 'utf8'");

// 检测连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
} 

$openId = $_POST["openId"];
$name = $_POST["name"];
$studentId = $_POST["studentId"];
$gender = $_POST["gender"];
$phone = $_POST["phone"];
$year = $_POST["year"];
// $major = $_POST["major"];
$motto = $_POST["motto"];

$res = $conn->query("UPDATE Users SET 
					name='$name',
					studentId='$studentId',
					gender='$gender', 
					phone='$phone',
					year='$year',
					-- major='$major',
					motto='$motto'
					WHERE openId='$openId'");

if ($res) {
	echo "200;Update successfully";
}else {
	echo "500;Update failed";
}

$conn->close();
?>