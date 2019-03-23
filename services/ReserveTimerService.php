<?php
include_once '../util/publicTool.php';

//release seat when time out. INCLUDE: cancelRsv.php checkState_B.php
$conn = connectMysql();
// $reserve_timeout = 60 * 15; //second --15mins = 
$reserve_timeout = 60 * 0.1;

while (true) {
	$recheck_seat = $conn->query("SELECT * FROM Seats WHERE state=1");
	if ($recheck_seat->num_rows != 0) {
		while ($row = $recheck_seat->fetch_assoc()) {
			if(time() - $row['time_marker'] > $reserve_timeout){
				echo "[".date("m/d-h:i:sa")."] R-Check sid:",$row['seat_id'],"; state:timeout:",time() - $row['time_marker'],";\n";
				cancelRsv($row['mark_user']);
				//sent kickout succeed message;
			}else{
				echo "[".date("m/d-h:i:sa")."] R-Check sid:",$row['seat_id'],"; state:Haven't timeout,",time() - $row['time_marker'],";\n";
			}
		}
	}else{
		echo "[".date("m/d-h:i:sa")."] R-Donothing;\n";
	}
	sleep(30);
}

$conn->close();

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