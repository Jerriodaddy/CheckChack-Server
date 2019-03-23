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
//Get data from Client
$openId = $_POST['openId'];

$user = $conn->query("SELECT * FROM Users WHERE openId='$openId'");
if ($user->num_rows == 0) {
	echo "204;No such user";
	$conn->close();
	return;
}else {
	$row = $user->fetch_assoc();
	echo json_encode($row, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
}

$conn->close();
?>