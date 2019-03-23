<?php
//check stat repeatedly. INCLUDE: checkState_B.php kick_C.php
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
$openId = $_POST['openId'];
$kickout_time = 60*20; //second

$recheck_seat = $conn->query("SELECT * FROM Seats WHERE seat_id='$scan_seat'");
$row = $recheck_seat->fetch_assoc();
$owner = $row['curr_user_id'];

$start_time = time();
while (time() - $start_time < $kickout_time) {
	sleep(3);
	if(checkstate_user($scan_seat, 0, 'NULL')){
		//Owner leave
		break;
	}
	if(checkstate_user($scan_seat, 2, $owner)){
		//release seat and send message to user
		//sendMessage(Kickout Fail);
		echo "Kick out fail";
		$conn->close();
		return;
	}
}
//Time out
kickout($openId,$scan_seat);
//Send result to user.
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

function kickout($openId,$scan_seat){
	global $conn;
	
	//DO kickout
	//get the old user info
	$tem_seat_info = $conn->query("SELECT * FROM Seats WHERE seat_id='$scan_seat'");
	$row = $tem_seat_info->fetch_assoc();
	$tem_seat_usr = $row['curr_user_id'];

	$check_openid = $conn->query("SELECT * FROM Users WHERE openId='$openId'");
	if ($check_openid->num_rows == 1) {
		//check does the user already has a seat?
		$row = $check_openid->fetch_assoc();
		if ($row['cur_seat'] == 'NULL') {//No
			//update last user
			$conn->query("UPDATE Users SET cur_state=0, cur_seat='NULL' WHERE openId='$tem_seat_usr'");
			//update new user
			$conn->query("UPDATE Users SET cur_state=1, cur_seat='$scan_seat' WHERE openId='$openId'");
			//update the seat
			$conn->query("UPDATE Seats SET state=1, curr_user_id='$openId', last_user_id='$tem_seat_usr' WHERE seat_id='$scan_seat'");
			echo "200;";
		}else{//Yes
			//update last user
			$conn->query("UPDATE Users SET cur_state=0, cur_seat='NULL' WHERE openId='$tem_seat_usr'");
			//update the seat
			$conn->query("UPDATE Seats SET state=0, curr_user_id='NULL', last_user_id='$tem_seat_usr' WHERE seat_id='$scan_seat'");
			echo "206;Set this free";
		}
	}else{
		echo "500;Users error";
	}
}

?>