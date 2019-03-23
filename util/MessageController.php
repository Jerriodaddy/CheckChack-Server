<?php
include_once 'publicTool.php';
include_once 'FormIdManager.php';

// $test = new MessageController();
// $test->sendReserveMesg(a,a,a);

class MessageController{
  public $touser;
  public $form_id;
  function __construct($openId){
    $this->touser = $openId;
    $fm = new FormIdManager();
    $this->form_id = $fm->takeOne($openId);
  }

  public function sendReserveMesg($reserve_time,$checked_seat){
    $conn = connectMysql();

    $access_token = getAccess_token();
    // $touser = $_POST['touser']; //openid
    // $form_id = $_POST['form_id'];
    // $reserve_time = $_POST['reserve_time'];
    // $checked_seat = $_POST['checked_seat'];
    $template_id = 'y_34n7rmS9_SVNgmIQllQnFMXFwq1-8kx6VMdNINRs4';
    $time = date('Y-m-d h:i:s', time());

    $num = '1';
    $recheck_seat = $conn->query("SELECT * FROM Seats WHERE seat_id='$checked_seat'");
    $row = $recheck_seat->fetch_assoc();
    if (strpos($row['type'],"sin") >= 0 ) {
        $num = '1';
    }else if (strpos($row['type'],"duo") >= 0 ){
        $num = '2';
    }else{
        $num = '3+';
    }
    // page  点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,（示例index?foo=bar）。该字段不填则模板无跳转。
    $sendData = array(
      'touser'=> $this->touser,
      'template_id'=> $template_id,
      'form_id'=> $this->form_id,
      'data'=>array(
        'keyword1'=> array(
          'value'=> $checked_seat
        ),
        'keyword2'=> array(
          'value'=> $time
        ),
        'keyword3'=> array(
          'value'=> $reserve_time
        ),
        'keyword4'=> array(
          'value'=> $num
        ),
        'emphasis_keyword'=> "keyword1.DATA"
      )
    );
    $sendData = json_encode($sendData);
    $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token;
    $result = httpRequest($url,$sendData,"POST");
    $conn->close();
    return $result;
  }

  public function sendStateChangedMesg($checked_seat,$state){
    $conn = connectMysql();

    $access_token = getAccess_token();
    $template_id = 'aYvHK1td9ZEFHbS3_7bQ3iexP1_7fRUVp1AbIF2hZJY';
    $time;
    switch ($state) {
      case 0:
        $time = "00:19:59";
        $tips = "您的座位被标记为无人，请及时返座，避免被系统自动释放并记录违规!";
        break;
      case 1:
        $time = "00:00:00";
        $tips = "您的座位已被释放，如有任何物品遗落请到管理员处认领。";
        break;
    }

    $sendData = array(
      'touser'=> $this->touser,
      'template_id'=> $template_id,
      'form_id'=> $this->form_id,
      'data'=>array(
        'keyword1'=> array(
          'value'=> $checked_seat
        ),
        'keyword2'=> array(
          'value'=> $time
        ),
        'keyword3'=> array(
          'value'=> $tips
        ),
        'emphasis_keyword'=> "keyword1.DATA"
      )
    );

    $sendData = json_encode($sendData);
    $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token;
    $result = httpRequest($url,$sendData,"POST");
    $conn->close();
    return $result;
  }

  public function sendFeedbackMesg($checked_seat,$res){
    $conn = connectMysql();

    $access_token = getAccess_token();
    $template_id = '-jNHDOLSrO85IkbD9xV6o36mlmpkwxaGlTMO4gcDZ48';
    $recheck_seat = $conn->query("SELECT * FROM Seats WHERE seat_id='$checked_seat'");
    $row = $recheck_seat->fetch_assoc();
    $seat_type = $row['type'];
    $operate;
    switch ($res) {
      case 0:
        $res = "失败";
        break;
      case 1:
        $res = "成功";
        $operate = "已为您自动预定，请15分钟内Checkin";
        break;
      case 2:
        $res = "成功";
        $operate = "该座位已释放";
        break;
    }

    $sendData = array(
      'touser'=> $this->touser,
      'template_id'=> $template_id,
      'form_id'=> $this->form_id,
      'data'=>array(
        'keyword1'=> array(
          'value'=> $checked_seat
        ),
        'keyword2'=> array(
          'value'=> $seat_type
        ),
        'keyword3'=> array(
          'value'=> $res
        ),
        'keyword4'=> array(
          'value'=> $operate
        ),
        'emphasis_keyword'=> "keyword1.DATA"
      )
    );

    $sendData = json_encode($sendData);
    $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token;
    $result = httpRequest($url,$sendData,"POST");
    $conn->close();
    return $result;
  }
}
?>
