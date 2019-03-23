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

$target=$_POST['target'];

// if ($target=='default') {
// 	echo "403;Error: target can not be null!";
// 	$conn->close();
// 	return;
// }

$data = array();
$roomSeats = $conn->query("SELECT * FROM Seats WHERE seat_id like '$target%'");
if ($roomSeats->num_rows > 0) {
	while ($row=$roomSeats->fetch_assoc()) {
		$data[]=$row;
	}
	echo json_encode($data);
}

$conn->close();
?>