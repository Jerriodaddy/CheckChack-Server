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
$scan_seat = $_POST['scan_seat'];
$expect_state = $_POST['expect_state'];
$expect_user = $_POST['expect_user'];

$res;
if ($expect_user == 'null') {
 	$res = checkstate($scan_seat, $expect_state));
} else{
	$res = checkstate_user($scan_seat, $expect_state, $expect_user));
}
echo $res;
return $res;

function checkstate($seat_id, $expect_state){
	global $conn;

	$recheck_seat = $conn->query("SELECT * FROM Seats WHERE seat_id='$seat_id'");
	$row = $recheck_seat->fetch_assoc();
	if ($row['state'] == $expect_state) {
		return true;
	}
	return false;
}

function checkstate_user($seat_id, $expect_state, $expect_user){
	global $conn;

	$recheck_seat = $conn->query("SELECT * FROM Seats WHERE seat_id='$seat_id'");
	$row = $recheck_seat->fetch_assoc();
	if ($row['state'] == $expect_state && $row['curr_user_id'] == $expect_user) {
		return true;
	}
	return false;
}

$conn->close();
?>