<?php
include_once 'util/MessageController.php';
include_once 'util/publicTool.php';

$conn = connectMysql();

//Get data from Client
$openId = $_POST['openId'];
$scan_seat = $_POST['scan_seat'];

//Check Seat is avaliable
$check_Avali = $conn->query("SELECT * FROM Seats WHERE seat_id='$scan_seat'");
$row = $check_Avali->fetch_assoc();
if ($row['state']==0) {
	//the seat is empty
	$check_openid = $conn->query("SELECT * FROM Users WHERE openId='$openId'");
	if ($check_openid->num_rows == 1) {
		$row = $check_openid->fetch_assoc();
		//Does this user have a seat?
		if ($row['cur_seat'] == 'NULL') { //No -> seat
			//update user
			$conn->query("UPDATE Users SET cur_state=2, cur_seat='$scan_seat' WHERE openId='$openId'");
			//update seat
			$conn->query("UPDATE Seats SET state=2, curr_user_id='$openId' WHERE seat_id='$scan_seat'");
			echo "200;Checkin succeed";
		}else{ //Yes -> scan the wrong QR code, also seat and release the reserved seat.
			//update user
			$conn->query("UPDATE Users SET cur_state=2, cur_seat='$scan_seat' WHERE openId='$openId'");
			//update seat
			$release_seat = $row['cur_seat'];
			$conn->query("UPDATE Seats SET state=0, curr_user_id='NULL', last_user_id='$openId' WHERE seat_id='$release_seat'");
			$conn->query("UPDATE Seats SET state=2, curr_user_id='$openId' WHERE seat_id='$scan_seat'");
			echo "200;Changing seat succeed, Your previous seat has been released.";
		}
	}else{
		echo "500;Users error!";
	}
}else if($row['state']==1){
	//the seat is reserved, check the user is the owner
	$check_openid = $conn->query("SELECT * FROM Users WHERE openId='$openId'");
	if ($check_openid->num_rows == 1) {
		$row = $check_openid->fetch_assoc();
		//Does this user scan the right code?
		if ($row['cur_seat'] == $scan_seat) {
			//update user
			$conn->query("UPDATE Users SET cur_state=2, cur_seat='$scan_seat' WHERE openId='$openId'");
			//update seat
			$conn->query("UPDATE Seats SET state=2 WHERE seat_id='$scan_seat'");
			//***!!!Kill reserveTimer in Java version
			echo "200;Good! Checkin succeed";
		}else{
			echo "406;This seat has been reserved";
		}
	}else{
		echo "406;This seat has been reserved";
	}
}else if($row['state']==2){
	echo "300;This seat has owner, are you sure kick s/he out?";
	// $conn->query("UPDATE Seats SET state=3 WHERE seat_id='$scan_seat';");
}else{//operating
	if($row['curr_user_id']==$openId){
		//owner back, restore seat state
		$conn->query("UPDATE Seats SET state=2 WHERE seat_id='$scan_seat'");
		echo "200;Re-checkin succeed";

		$marker = $row['mark_user'];
		$message = new MessageController($marker);
		echo $message->sendFeedbackMesg($scan_seat,0);
	}else{
		echo "406;This seat may be released in a few mins";
	}
	
}

$conn->close();
?>