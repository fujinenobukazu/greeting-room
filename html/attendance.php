<?php
session_start();

// ログイン画面を経由しているかチェック。
if(!isset($_SESSION['User'])){
  header('Location: /creative_php/html/login.php');
  exit;
}

require_once "../object/config.php";
require_once "../object/User.php";

try{
  // DB接続
  $User = new User($host, $dbname, $user, $pass);
  $User->connectDb();

  if(isset($_POST)){
    if(isset($_POST['inTime'])){
      $User->addAttendanceIn($_POST);
    }elseif(isset($_POST['outTime'])){
      $User->editAttendanceOut($_POST);
    }elseif(isset($_POST['breakTime'])){
      $User->editAttendanceBreak($_POST);
    }
  }

  // 本日の勤怠レコードを取得
  $result = $User->IdDay($_SESSION['User']);
  $result_a_only = $User->findAttendanceOnly($result);


}catch(PDOException $e){
  print('PDOException:' . $e->getMessage());
}

// 接続解除
$User = null;
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>社員専用サイト 勤怠打刻</title>
<script type="text/javascript" src="../js/jquery.js"></script>

<link rel="stylesheet" type="text/css" href="../css/base.css?v=2">
<link rel="stylesheet" type="text/css" href="../css/attendance.css?v=2">

</head>
<body>
  <script>
  $(function(){
      // ここに処理を記入
      setInterval(function(){
        $.ajax({
          url: '../object/real_time.php',
          dataType: 'html',
          success: function(data){
            $('.real_time').html(data);
          },
          error :function(data){
            aleat('error');
          }
        });
      },100);
  });
  </script>

  <!-- ↓ヘッダー↓ -->
  <?php require("../object/header.php"); ?>
  <!-- ↑ヘッダー↑ -->

<main>
  <div class="link_btn">
    <a href="mypage.php" class="btn return">勤怠編集</a>
  </div>
</main>

<!-- ここからした -->
<section>
  <p class="title">--勤怠入力--</p>
  <p class="real_time"></p>
  <div class="attendance_btn">
    <form action="attendance.php" method="post">
    <input type="hidden" name="day" value="<?=date("Y-m-d")?>">
    <input type="hidden" name="in_time" value="<?=date("H:i:s")?>">
    <input type="hidden" name="id" value="<?=$_SESSION['User']['id']?>">
    <input type="hidden" name="inTime" value="true">
    <input type="submit" value="出勤" class="in_time" <?php if($result_a_only) if($result_a_only['in_time']) echo"disabled"?>>
    </form>

    <form action="attendance.php" method="post">
    <input type="hidden" name="day" value="<?=date("Y-m-d")?>">
    <input type="hidden" name="out_time" value="<?=date("H:i:s")?>">
    <input type="hidden" name="id" value="<?=$_SESSION['User']['id']?>">
    <input type="hidden" name="outTime" value="true">
    <input type="submit" value="退勤" class="out_time" <?php if(empty($result_a_only['in_time']) || !empty($result_a_only['out_time']) ) echo"disabled"?> >
    </form>
  </div>

  <div class="attendance_btn bordertop">
    <form action="attendance.php" method="post">
    <input type="hidden" name="day" value="<?=date("Y-m-d")?>">
    <input type="number" name="break_time">
    <input type="hidden" name="id" value="<?=$_SESSION['User']['id']?>">
    <input type="hidden" name="breakTime" value="true">
    <input type="submit" value="休憩" class="break_time" <?php if(empty($result_a_only['in_time']) || !empty($result_a_only['break_time']) ) echo"disabled"?> >
    </form>
  </div>
  <p class="p_center">※休憩時間を分単位で入力して下さい。</p>

  <div class="attendance_btn bordertop">
    <div>出勤時刻：<?php if($result_a_only) echo $result_a_only['in_time']?></div>
    <div>退勤時刻：<?php if($result_a_only) echo $result_a_only['out_time']?></div>
    <div>休憩時間：<?php if($result_a_only) echo $result_a_only['break_time']?></div>
  </div>
</section>

  <?php require("../object/footer.php"); ?>
</body>
</html>
