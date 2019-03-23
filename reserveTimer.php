<?php
//release seat when time out. INCLUDE: cancelRsv.php checkState_B.php
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
$checked_seat = $_POST['checked_seat'];
$form_id = $_POST['form_id'];
$reserve_time = 15 * 60; //second --15mins = 

$start_time = time();
while (time() - $start_time < $reserve_time) {
	sleep(1);
}
if(checkstate_user($checked_seat, 1, $openId)){
	//release seat and send message to user
	cancelRsv($openId);
	//sendMessage();
}
$conn->close();

function checkstate_user($seat_id, $expect_state, $expect_user){
	global $conn;

	$recheck_seat = $conn->query("SELECT * FROM Seats WHERE seat_id='$seat_id'");
	$row = $recheck_seat->fetch_assoc();
	if ($row['state'] == $expect_state && $row['curr_user_id'] == $expect_user) {
		return true;
	}
	return false;
}

function cancelRsv($openId){
	global $conn;
	//get the this user's seat
	$user_info = $conn->query("SELECT * FROM Users WHERE openId='$openId'");
	$row = $user_info->fetch_assoc();
	$the_seat = $row['cur_seat'];

	//update the seat
	$conn->query("UPDATE Seats SET state=0, curr_user_id='NULL' WHERE seat_id='$the_seat'"); // little dif with checkout.php: don't change the last_user_id.
	//update last user
	$conn->query("UPDATE Users SET cur_state=0, cur_seat='NULL' WHERE openId='$openId'");
}

?>