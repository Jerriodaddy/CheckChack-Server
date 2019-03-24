<?php
include_once 'publicTool.php';

// $test = new FeedbackManager();
// $test->deleteTable();
// $test->creatTable();
// $test->delete(16);
// $res = $test->add("A","Hello");
// $res = $test->add("oWbK45RpARbfBTLLVSl1nptvADL8","World");
// echo "\n",$res;

// if($test->takeOne()==NULL){
// 	echo "NULL";
// }

class FeedbackManager{
	function __construct(){
		# code...
	}

	public function creatTable(){
		$conn = connectMysql();
		$creat = "CREATE TABLE IF NOT EXISTS `Feedbacks` (
				`num` int AUTO_INCREMENT,
				`openId` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
				`name` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT 'NULL',
				`create_time` timestamp DEFAULT CURRENT_TIMESTAMP,
				`feedback` varchar(512),
				PRIMARY KEY (`num`),
				KEY `num` (`num`) USING BTREE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Feedbacks';";
		$creat_res = mysqli_query($conn, $creat);
		if (!$creat_res) {
			die('数据表建立失败: ' . mysqli_error($conn));
		}
		echo "数据表建立成功\n";
		$conn->close();
		return true;
	}

	public function deleteTable(){
		$conn = connectMysql();
		$del_res = mysqli_query( $conn, "DROP TABLE Feedbacks" );
		if(!$del_res ){
		  die('数据表删除失败: ' . mysqli_error($conn));
		}
		echo "数据表删除成功\n";
		$conn->close();
		return true;
	}

	public function add($openId,$feedback){
		$conn = connectMysql();
		
		//get Name
		$get_name = $conn->query("SELECT * from Users where openId='$openId'");
		$row = $get_name->fetch_assoc();
		$name = $row['name'];

		$sql = "INSERT INTO Feedbacks (openId, name, feedback) 
		VALUES ('$openId', '$name', '$feedback')";

		if ($conn->query($sql) === TRUE) {
		    echo "新记录插入成功\n";
		    $conn->close();
			return true;
		}
		echo "Error: " . $sql . "<br>" . $conn->error;
		$conn->close();
		return false;
	}

	public function delete($num){
		$conn = connectMysql();
		
		$sql = "DELETE from Feedbacks where num='$num'";

		if ($conn->query($sql) === TRUE) {
		    echo "num:",$num," 删除成功\n";
		    $conn->close();
			return true;
		}
		echo "Error: " . $sql . "<br>" . $conn->error;
		$conn->close();
		return false;
	}
}


?>