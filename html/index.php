<?php
session_start();

// ログイン画面を経由しているかチェック。
if(!isset($_SESSION['User'])){
  header('Location: /creative_php/html/login.php');
  exit;
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>社員専用サイト メインメニュー</title>

<link rel="stylesheet" type="text/css" href="../css/base.css?v=2">
<link rel="stylesheet" type="text/css" href="../css/index.css?v=2">

</head>
<body>
  <!-- ↓ヘッダー↓ -->
  <?php require("../object/header.php"); ?>
  <!-- ↑ヘッダー↑ -->

  <section class="main">
    <img src="../img/message.png">
  </section>
  <section class="mgn_lft">
    <img src="../img/mission.png">
  </section>

  <?php require("../object/footer.php"); ?>
</body>
</html>
