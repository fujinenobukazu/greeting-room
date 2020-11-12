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

  // 登録処理
  if(isset($_POST['add_user'])){
    $User->addUser($_POST);
  }

  if(isset($_GET['display'])){
    if($_GET['display']=="all"){
      $result = $User->findUsersAll();
    }

  }elseif(isset($_GET['assignment_id'])){
    $result = $User->findUsers($_GET);
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
<title>社員専用サイト 社員一覧</title>

<link rel="stylesheet" type="text/css" href="../css/base.css?v=2">
<link rel="stylesheet" type="text/css" href="../css/team_employee.css?v=2">

</head>
<body>
  <!-- ↓ヘッダー↓ -->
  <?php require("../object/header.php"); ?>
  <!-- ↑ヘッダー↑ -->
  <main>
    <div class="link_btn">
      <a href="team.php" class="btn return">戻る</a>
    </div>
    <table>
      <tr>
        <th>配属先ID</th>
        <th>配属先名</th>
        <th>ステータス</th>
        <th>役職</th>
        <th>名前</th>
        <th>Email</th>
        <th>Tell</th>
        <th></th>
      </tr>

      <?php while($row = $result->fetch()): ?>
      <tr>
        <td><?=$row['assignment_id']?></td>
        <td><?=$row['assignment_name']?></td>
        <td><?=$row['status_name']?></td>
        <td><?=$row['position']?></td>
        <td><?=$row['name']?></td>
        <td><?=$row['email']?></td>
        <td><?=$row['tell']?></td>
        <td>
          <?php if($_SESSION['User']['role']>=1):?>
          <form action="team_employee_data.php" method="post">
          <input type="hidden" name="year" value="<?=date('Y')?>">
          <input type="hidden" name="month" value="<?=date('m')?>">
          <input type="hidden" name="id" value="<?=$row['id']?>">
          <input type="submit" value="詳　細">
          </form>
          <?php endif;?>
        </td>
      </tr>
      <?php endwhile; ?>

    </table>
  </main>
  <!-- ↓フッター↓ -->
  <?php require("../object/footer.php"); ?>
  <!-- ↑フッター↑ -->
</body>
</html>
