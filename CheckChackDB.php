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

$sql = "SELECT * FROM Seats";
mysqli_query($conn, "set names 'utf8'");//不写这句有可能乱码
$result = mysqli_query($conn, $sql);

//重新设置输出格式
class Seat{
	public $seat_id;
	public $state;
	public $type;
	public $loca_y;
	public $loca_x;
	public $create_time;
	public $last_visit_time;
	public $curr_user_id;
	public $last_user_id;
}
$data = array();
if (mysqli_num_rows($result) > 0) {
	while($row = mysqli_fetch_assoc($result)) {
		$seat = new Seat();
		$seat->seat_id = $row["seat_id"];
		$seat->state = $row["state"];
		$seat->type = $row["type"];
		$seat->loca_y = $row["loca_y"];
		$seat->loca_x = $row["loca_x"];
		$seat->create_time = $row["create_time"];
		$seat->last_visit_time = $row["last_visit_time"];
		$seat->curr_user_id = $row["curr_user_id"];
		$seat->last_user_id = $row["last_user_id"];
		$data[] = $seat;
	}
	echo json_encode($data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);//将请求结果转换为json格式，for微信
}

$conn->close();
?>