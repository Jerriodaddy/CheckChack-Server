<?php
include_once '../util/MessageController.php';
include_once '../util/publicTool.php';

session_start();

//check stat repeatedly. INCLUDE: checkState_B.php kick_C.php
$conn = connectMysql();
// $kickout_timeout = 60 * 20; //second
$kickout_timeout = 60 * 0.1; //second

while (true) {
	$recheck_seat = $conn->query("SELECT * FROM Seats WHERE state=3");
	if ($recheck_seat->num_rows != 0) {
		while($row = $recheck_seat->fetch_assoc()){
			if(time() - $row['time_marker'] > $kickout_timeout){
				echo "[".date("m/d-h:i:sa")."] K-Check sid:",$row['seat_id'],"; state:timeout:",time() - $row['time_marker'],";\n";
				kickout($row['mark_user'],$row['seat_id']);
				//sent kickout succeed message;
			}else{
				echo "[".date("m/d-h:i:sa")."] K-Check sid:",$row['seat_id'],"; state:Haven't timeout,",time() - $row['time_marker'],";\n";
			}
		}
	}else{
		echo "[".date("m/d-h:i:sa")."] K-Donothing;\n";
	}
	sleep(30);
}

$conn->close();

function kickout($openId,$scan_seat){
	global $conn;
	
	//DO kickout
	//get the owner info
	$tem_seat_info = $conn->query("SELECT * FROM Seats WHERE seat_id='$scan_seat'");
	$row = $tem_seat_info->fetch_assoc();
	$tem_seat_usr = $row['curr_user_id'];

	$to_owner_message = new MessageController($tem_seat_usr);
	echo $to_owner_message->sendStateChangedMesg($scan_seat,1);

	$check_openid = $conn->query("SELECT * FROM Users WHERE openId='$openId'");
	if ($check_openid->num_rows == 1) {
		//check does the user already has a seat?
		$row = $check_openid->fetch_assoc();
		if ($row['cur_seat'] == 'NULL') {//No Set reserve.
			$time_marker = time();

			//update last user
			$conn->query("UPDATE Users SET cur_state=0, cur_seat='NULL' WHERE openId='$tem_seat_usr'");
			//update new user
			$conn->query("UPDATE Users SET cur_state=1, cur_seat='$scan_seat' WHERE openId='$openId'");
			//update the seat
			$conn->query("UPDATE Seats SET state=1, curr_user_id='$openId', last_user_id='$tem_seat_usr', time_marker='$time_marker', mark_user='$openId' WHERE seat_id='$scan_seat'");
			echo "200;";

			$message = new MessageController($openId);
			echo $message->sendFeedbackMesg($scan_seat,1);
		}else{//Yes
			//update last user
			$conn->query("UPDATE Users SET cur_state=0, cur_seat='NULL' WHERE openId='$tem_seat_usr'");
			//update the seat
			$conn->query("UPDATE Seats SET state=0, curr_user_id='NULL', last_user_id='$tem_seat_usr' WHERE seat_id='$scan_seat'");
			echo "206;Set this free";

			$message = new MessageController($openId);
			echo $message->sendFeedbackMesg($scan_seat,2);
		}
	}else{
		echo "500;User’s info error";
	}
}

// function checkstate_user($seat_id, $expect_state, $expect_user){
// 	global $conn;

// 	$recheck_seat = $conn->query("SELECT * FROM Seats WHERE seat_id='$seat_id'");
// 	$row = $recheck_seat->fetch_assoc();
// 	if ($row['state'] == $expect_state && $row['curr_user_id'] == $expect_user) {
// 		return true;
// 	}
// 	return false;
// }
?>