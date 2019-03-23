<?php
//配置APPID、APPSECRET
$APPID = "wxf561b40709d5a0b8"; 
$APPSECRET =  "6864e0c4e0324335895e91b873378df8"; 
//获取access_token
$access_token = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$APPID&secret=$APPSECRET";

//缓存access_token
 session_start();
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
 };

//连接数据库，为每个位置生成code
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

$seats = $conn->query("SELECT seat_id FROM Seats");
if ($seats->num_rows > 0) {
    while ($row = $seats->fetch_assoc()) {
        getQrCode($ACCESS_TOKEN, $row['seat_id'], $row['seat_id'].".png");
    }
} 
// getQrCode($ACCESS_TOKEN,"L405010","L405010.png");




function getQrCode($ACCESS_TOKEN,$scene,$filename){
    //构建请求二维码参数
    $url ="https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=$ACCESS_TOKEN";
    $data = json_encode(array("scene"=>'$scene'));
    // $data = '{
    //     "scene": "L405010"
    //     }';

    //POST参数
    $result = httpRequest($url,$data,"POST");
    //生成二维码
    file_put_contents($filename, $result);

    $base64_image ="data:image/jpeg;base64,".base64_encode( $result );
}

//把请求发送到微信服务器换取二维码
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