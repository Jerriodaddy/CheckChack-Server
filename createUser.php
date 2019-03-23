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

$openId = $_POST['openId'];

$check_openid = $conn->query("SELECT * FROM Users WHERE openId='$openId'");
if ($check_openid->num_rows == 0) {
	//Is new user
	if($conn->query("INSERT INTO Users (openId, cur_state, cur_seat)
				  VALUES ('$openId', 0, 'NULL')")){
		echo "201;New user created.";
	}
	else{
		echo "500;Create new user faild!";
	}
}else{
	echo "200;User already exist.";
}

$conn->close();
?>