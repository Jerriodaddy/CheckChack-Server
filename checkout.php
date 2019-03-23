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
//Get data from Client
$openId = $_POST['openId'];
//get the this user's seat
$user_info = $conn->query("SELECT * FROM Users WHERE openId='$openId'");
$row = $user_info->fetch_assoc();
$the_seat = $row['cur_seat'];

//update the seat
$conn->query("UPDATE Seats SET state=0, curr_user_id='NULL', last_user_id='$openId' WHERE seat_id='$the_seat'");
//update last user
$conn->query("UPDATE Users SET cur_state=0, cur_seat='NULL' WHERE openId='$openId'");
echo "200;checkout successfully";

$conn->close();
?>