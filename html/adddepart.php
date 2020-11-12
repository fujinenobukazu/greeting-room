<?php
session_start();

if(!isset($_SESSION['User'])){
  header('Location: /creative_php/html/login.php');
  exit;
}elseif($_SESSION['User']['role'] < 2){
  header('Location: /creative_php/html/index.php');
  exit;
}

require_once "../object/config.php";
require_once "../object/User.php";

try{
  // DB接続
  $User = new User($host, $dbname, $user, $pass);
  $User->connectDb();

  if(isset($_POST['add'])){
    $User->addDepart($_POST);
  }elseif(isset($_POST['dlt_depart'])){
    $User->deleteDepart($_POST);
  }elseif(isset($_POST['edit_depart'])){
    $result_depart_only = $User->findDepartOnly($_POST);
  }elseif(isset($_POST['edit_depart_c'])){
    $User->editDepart($_POST);
  }

  $result_depart = $User->findDepart();

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
<title>社員専用サイト 部署情報編集</title>

<link rel="stylesheet" type="text/css" href="../css/base.css?v=2">
<link rel="stylesheet" type="text/css" href="../css/addteam.css?v=2">

</head>
<body>
  <!-- ↓ヘッダー↓ -->
  <?php require("../object/header.php"); ?>
  <!-- ↑ヘッダー↑ -->
  <main>
    <div class="link_btn">
      <a href="adddepart.php" class="btn main">部署編集</a>
      <a href="addassign.php" class="btn sub">配属先編集</a>
      <a href="team.php" class="btn return">一覧へ戻る</a>
    </div>

    <!-- 勤怠データ -->
    <table class="attendance">

      <tr class="time_data_title">
        <td>部署ID</td>
        <td>部署名</td>
        <td></td>
        <td>配属先数</td>
        <td></td>
      </tr>

      <!-- 登録フォーム -->
      <tr class="">
        <form action="adddepart.php" method="post">
        <td><input type="text" name="department_id" value=
          "<?php if(isset($result_depart_only)) echo "$result_depart_only[department_id]"; ?>"
          <?php if(isset($result_depart_only)) if($result_depart_only['count'] > 0) echo"readonly"?> ></td>
        <td><input type="text" name="department_name" value="<?php if(isset($result_depart_only)) echo "$result_depart_only[department_name]"; ?>"></td>
        <td></td>
        <td><?php if(isset($result_depart_only)) echo "$result_depart_only[count]"; ?></td>
        <!-- 登録か変更かの条件分岐 -->
        <?php if(isset($_POST['edit_depart'])):?>
        <input type="hidden" name="old_department_id" value="<?=$result_depart_only['department_id']?>">
        <input type="hidden" name="edit_depart_c" value="true">
        <?php else:?>
        <input type="hidden" name="add" value="true">
        <?php endif;?>

        <td>
          <div class="p_center">
          <input type="submit" value="登録" onClick="if(!confirm('こちらの内容で変更致しますか？')) return false;">
        </form>

          <?php if(isset($_POST['edit_depart'])):?>
          <form action="adddepart.php" method="post" class="inline">
          <input type="submit" value="戻る">
          </form>
          <?php endif;?>
        </div>
        </td>

      </tr>

      <!-- 編集ボタンが押された時は下記を非表示 -->
      <?php if(!isset($_POST['edit_depart'])):?>
      <?php while($row = $result_depart->fetch()): ?>
      <tr class="time_data">
        <td><?=$row['department_id']?></td>
        <td><?=$row['department_name']?></td>
        <td></td>
        <td><?=$row['count']?></td>
        <td>
          <div class="p_center">
            <?php if($_SESSION['User']['role']==2):?>
            <!-- 編集　POST送信ボタン -->
            <form action="adddepart.php" method="post">
              <input type="hidden" name="department_id" value="<?=$row['department_id']?>">
              <input type="hidden" name="edit_depart" value="true">
              <input type="submit" value="編集">
            </form>

            <!-- 削除　POST送信ボタン -->
            <form action="adddepart.php" method="post">
              <input type="hidden" name="department_id" value="<?=$row['department_id']?>">
              <input type="hidden" name="dlt_depart" value="true">
              <input type="submit" value="削除"
              onClick="if(!confirm('<?=$row['department_name']?>を削除致しますか？')) return false;"
              <?php if($row['count'] > 0) echo"disabled"?>
              >
            </form>
            <?php endif;?>
          </a>
        </div>
        </td>
      </tr>
      <?php endwhile; ?>
    <?php endif;?><!-- 編集ボタンが押された時は上記を非表示 -->

    </table>

  </main>
  <!-- ↓フッター↓ -->
  <?php require("../object/footer.php"); ?>
  <!-- ↑フッター↑ -->
</body>
</html>
