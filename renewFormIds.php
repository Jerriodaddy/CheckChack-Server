<?php
include_once 'util/FormIdManager.php';
include_once 'util/publicTool.php';
$conn = connectMysql();

$fm = new FormIdManager();
$fm->deleteTable();
$fm->creatTable();

$conn->close();
?>