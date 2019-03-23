<?php
include_once '../util/publicTool.php';
include_once '../util/FormIdManager.php';

$conn = connectMysql();

$updater = new FormIdManager();
// $timeout = 60 * 60 * 24 * 5;
$timeout = 10;

while (true) {
	$check = $conn->query("SELECT * FROM FormIds");
	if ($check->num_rows != 0) {
		while ($row = $check->fetch_assoc()) {
			if(time() - $row['create_time'] > $timeout){
				echo "[".date("m/d-h:i:sa")."] U-Check formId:",$row['formId'],"; state:timeout:",time() - $row['create_time'],";\n";
				$formId=$row['formId'];
				$updater->delete($formId);
				//sent kickout succeed message;
			}else{
				echo "[".date("m/d-h:i:sa")."] U-Check formId:",$row['formId'],"; state:Haven't timeout,",time() - $row['create_time'],";\n";
			}
		}
	}else{
		echo "[".date("m/d-h:i:sa")."] U-Donothing;\n";
	}
	sleep(60 * 60);
}

$conn->close();

?>