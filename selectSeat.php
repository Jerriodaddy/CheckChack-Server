<?php
include_once 'util/MessageController.php';
include_once 'util/publicTool.php';
session_start();//缓存access_token in publicTool.php-getAccess_token

$reserve_time = "15mins";

$conn = connectMysql();
//Get data from Client
$checked_seat = $_POST['checked_seat'];
$openId = $_POST['openId'];
$time_marker = time();

//Synchronization checking
//is this seat still empty?
$is_empty = $conn->query("SELECT * FROM Seats WHERE seat_id='$checked_seat'");
$row = $is_empty->fetch_assoc();
if ($row['state'] != 0) {
	echo "409;Page need reloading";
	$conn->close();
	return;
}

//will be reconsidered in the future.
$check_openid = $conn->query("SELECT * FROM Users WHERE openId='$openId'");
if ($check_openid->num_rows == 0) {
	echo "500;No Users Info";
}else{
	$row = $check_openid->fetch_assoc();
	//Does this user already have a seat?
	if ($row['cur_seat'] == 'NULL') {
		//update Users
		$conn->query("UPDATE Users SET cur_state=1, cur_seat='$checked_seat' WHERE openId='$openId'");
		//update Seats
		$conn->query("UPDATE Seats SET state=1, curr_user_id='$openId', time_marker='$time_marker', mark_user='$openId' WHERE seat_id='$checked_seat'");

		//***!!!Do fork here to reserveTimer in java.
		echo "200;";
		//sentMessage
		$message = new MessageController($openId);
		$message->sendReserveMesg($reserve_time,$checked_seat);
	}else{
		echo "406;You already has a seat(".$row['cur_seat'].") You can only have one seat.";
	}
}
$conn->close();
?>