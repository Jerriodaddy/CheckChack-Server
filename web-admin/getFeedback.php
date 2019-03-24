<?php
include_once '../util/publicTool.php';
$conn = connectMysql();
$res = $conn->query("SELECT * from Feedbacks");

if ($res->num_rows != 0) {
	echo "<table border=1><tr>";
	echo "<th>num</th><th>name</th><th>create_time</th><th>feedback</th>";
	echo "</tr>";
	
	while ($row = $res->fetch_assoc()) {
		echo "<tr>";
		echo "<td>".$row['num']."</td>
			  <td>".$row['name']."</td>
			  <td>".$row['create_time']."</td>
			  <td>".$row['feedback']."</td>";
		echo "</tr>";
	}
}else{
	echo "No feedback yet.";
}

?>