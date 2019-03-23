<?php
include_once 'util/MessageController.php';
include_once 'util/publicTool.php';
session_start();

$conn = connectMysql();

//Get data from Client
// $scan_seat = $_GET['scan_seat'];
// $mark_user = $_GET['openId'];
$scan_seat = $_POST['scan_seat'];
$mark_user = $_POST['openId'];
$time_marker = time();

$conn->query("UPDATE Seats SET state=3, time_marker='$time_marker', mark_user='$mark_user' WHERE seat_id='$scan_seat';");
echo "200;Set operating successfully";

//***Send a message to the owner.
$res = $conn->query("SELECT * FROM Seats WHERE seat_id='$scan_seat';");
if ($res->num_rows != 0) {
	$row = $res->fetch_assoc();
	$openId = $row['curr_user_id'];
	$message = new MessageController($openId);
	echo $message->sendStateChangedMesg($scan_seat,0);
}else{
	echo "No this user;\n";
}

$conn->close();
?>