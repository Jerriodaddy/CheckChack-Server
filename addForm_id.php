<?php
include_once 'util/FormIdManager.php';
include_once 'util/publicTool.php';
$conn = connectMysql();
//Get data from Client
$openId = $_POST['openId'];
$form_id = $_POST['form_id'];

$fm = new FormIdManager();
$fm->add($form_id,$openId);
echo $form_id;

$conn->close();
?>