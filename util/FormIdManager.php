<?php
include_once 'publicTool.php';

// $test = new FormIdManager();
// $test->creatTable();
// $test->deleteTable();
// $res = $test->add("a");
// $res = $test->add("aaa");
// echo "\n",$res;
// $test->delete("aa");
// if($test->takeOne()==NULL){
// 	echo "NULL";
// }

class FormIdManager{
	function __construct(){
		# code...
	}

	public function creatTable(){
		$conn = connectMysql();
		$creat = "CREATE TABLE IF NOT EXISTS `FormIds` (
				`formId` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
				`openId` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
				`create_time` int NOT NULL,
				PRIMARY KEY (`formId`),
				KEY `formId` (`formId`) USING BTREE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Avaliable FormId';";
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
		$del_res = mysqli_query( $conn, "DROP TABLE FormIds" );
		if(!$del_res ){
		  die('数据表删除失败: ' . mysqli_error($conn));
		}
		echo "数据表删除成功\n";
		$conn->close();
		return true;
	}

	public function add($formId,$openId){
		$conn = connectMysql();
		
		$create_time = time();
		$sql = "INSERT INTO FormIds (formId, openId, create_time) 
		VALUES ('$formId', '$openId', '$create_time')";

		if ($conn->query($sql) === TRUE) {
		    echo "新记录插入成功\n";
		    $conn->close();
			return true;
		}
		echo "Error: " . $sql . "<br>" . $conn->error;
		$conn->close();
		return false;
	}

	public function delete($formId){
		$conn = connectMysql();
		
		$sql = "DELETE from FormIds where formId='$formId'";

		if ($conn->query($sql) === TRUE) {
		    echo "FormId:",$formId," 删除成功\n";
		    $conn->close();
			return true;
		}
		echo "Error: " . $sql . "<br>" . $conn->error;
		$conn->close();
		return false;
	}

	public function takeOne($openId){
		$conn = connectMysql();
		$formId;
		$res = $conn->query("SELECT * from FormIds where openId='$openId';");
		if ($res->num_rows != 0) {
			$row = $res->fetch_assoc();
			$formId = $row['formId'];
			$this->delete($formId);
		}
		$conn->close();
		echo "Took formId: ".$formId." from".$openId."\n";
		return $formId;
	}
}


?>