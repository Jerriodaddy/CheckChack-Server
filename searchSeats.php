<?php
/*
This function echo an Array eg. {floor_num: "L1", empty_num: 0, percentage: 0}
*/

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

$target=$_POST['target'];
$accuracy=$_POST['accuracy'];

if ($target=='default') {
	echo "403;Error: target can not be null!";
	$conn->close();
	return;
}

class Space{
	public $space_num;
	public $empty_num;
	public $percentage;
}

$data = array();
for ($i=0; $i < pow(10,$accuracy); $i++) {
	//eg. 1 -> 01, 2 -> 02 or 1 -> 001 ... ACCURACY CANNOT MORE THAN 3
	if ($i<10) {
		$num = str_repeat("0",$accuracy-1).$i;
	// }else if ($i<100 && $accuracy == 3) {
	// 	$num = str_repeat("0",$accuracy-2).$i;
	}else{
		$num = $i;
	}
	$allSeats = $conn->query("SELECT * FROM Seats WHERE seat_id like '$target$num%'");
	if ($allSeats->num_rows>0) {
		$emptySeats = $conn->query("SELECT * FROM Seats WHERE seat_id like '$target$num%' and state='0'");
		$space = new Space();
		$space->space_num = $target.$num;
		$space->empty_num = $emptySeats->num_rows;
		if ($allSeats->num_rows == 0) {
			$space->percentage = 0;
		}else{
			$space->percentage = round($emptySeats->num_rows/$allSeats->num_rows,2)*100;
		}
		$data[]=$space;	
	}
}
echo json_encode($data);

$conn->close();
?>