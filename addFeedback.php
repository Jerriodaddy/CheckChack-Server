<?php
include_once 'util/FeedbackManager.php';
include_once 'util/publicTool.php';
$conn = connectMysql();
//Get data from Client
$openId = $_POST['openId'];
$feedback = $_POST['feedback'];

$fb = new FeedbackManager();
$fb->add($openId,$feedback);

$conn->close();
?>