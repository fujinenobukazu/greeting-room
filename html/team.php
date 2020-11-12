<?php
session_start();

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

  if($_GET){
    $result = $User->findAssign($_GET);
  }else{
    $result = $User->findDepart();
  }

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
<title>社員専用サイト Teamメニュー</title>

<link rel="stylesheet" type="text/css" href="../css/base.css?v=2">
<link rel="stylesheet" type="text/css" href="../css/team.css?v=2">

</head>
<body>
  <!-- ↓ヘッダー↓ -->
  <?php require("../object/header.php"); ?>
  <!-- ↑ヘッダー↑ -->
  <main>
    <div class="link_btn">
      <a href="#" class="btn orange">組織図</a>
      <a href="team_employee.php?display=all" class="btn blue">社員一覧</a>
      <?php if($_SESSION['User']['role']==2):?>
      <a href="adduser.php" class="btn green">新規社員登録</a>
      <a href="adddepart.php" class="btn green">Team情報編集</a>
      <?php endif;?>
      <?php if($_GET):?>
        <a href="team.php" class="btn return">戻る</a>
      <?php endif;?>
    </div>
    <section class="team_btn">
      <?php if($_GET):?>
        <!-- 配属先テーブル表示 -->
        <?php while($row = $result->fetch()): ?>
        <a href="team_employee.php?assignment_id=<?=$row['assignment_id']?>" class="btn assignment"><?= $row["assignment_name"] ?></a>
        <?php endwhile; ?>

      <?php else:?>
        <!-- 部署テーブル表示 -->
        <?php while($row = $result->fetch()): ?>
        <a href="team.php?department_id=<?=$row['department_id']?>" class="btn department"><?= $row["department_name"] ?></a>
        <?php endwhile; ?>
      <?php endif;?>
    </section>
  </main>

  <!-- ↓フッター↓ -->
  <?php require("../object/footer.php"); ?>
  <!-- ↑フッター↑ -->
</body>
</html>
