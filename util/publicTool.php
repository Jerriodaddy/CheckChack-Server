<?php
function connectMysql(){
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
    return $conn;
}

function getAccess_token(){
    //配置APPID、APPSECRET
    $APPID = "wxf561b40709d5a0b8"; 
    $APPSECRET =  "6864e0c4e0324335895e91b873378df8"; 
    //获取access_token
    $access_token = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$APPID&secret=$APPSECRET";

    //缓存access_token
    // session_start();
     $_SESSION['access_token'] = "";
     $_SESSION['expires_in'] = 0;

     $ACCESS_TOKEN = "";
     if(!isset($_SESSION['access_token']) || (isset($_SESSION['expires_in']) && time() > $_SESSION['expires_in']))
     {
         $json = httpRequest( $access_token );
         $json = json_decode($json,true); 
         // var_dump($json);
         $_SESSION['access_token'] = $json['access_token'];
         $_SESSION['expires_in'] = time()+7200;
         $ACCESS_TOKEN = $json["access_token"]; 
     } 
     else{
         $ACCESS_TOKEN =  $_SESSION["access_token"];
     }
     return $ACCESS_TOKEN;
  }

function httpRequest($url, $data='', $method='GET'){
    $curl = curl_init();  
    curl_setopt($curl, CURLOPT_URL, $url);  
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);  
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);  
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);  
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);  
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);  
    if($method=='POST')
    {
        curl_setopt($curl, CURLOPT_POST, 1); 
        if ($data != '')
        {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);  
        }
    }

    curl_setopt($curl, CURLOPT_TIMEOUT, 30);  
    curl_setopt($curl, CURLOPT_HEADER, 0);  
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);  
    curl_close($curl);  
    return $result;
  }
?>