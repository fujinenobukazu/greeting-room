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
// assignのスペルミスチェック
  if(isset($_POST['add'])){
    $User->addAssign($_POST);
  }elseif(isset($_POST['dlt_assign'])){
    $User->deleteAssign($_POST);
  }elseif(isset($_POST['edit_assign'])){
    $result_assign_only = $User->findAssignOnly($_POST);
  }elseif(isset($_POST['edit_assign_c'])){
    $User->editAssign($_POST);
  }

  $result_assign = $User->find_A_U();
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
<title>社員専用サイト 社員詳細情報</title>

<link rel="stylesheet" type="text/css" href="../css/base.css?v=2">
<link rel="stylesheet" type="text/css" href="../css/addteam.css?v=2">

</head>
<body>
  <!-- ↓ヘッダー↓ -->
  <?php require("../object/header.php"); ?>
  <!-- ↑ヘッダー↑ -->
  <main>
    <div class="link_btn">
      <a href="adddepart.php" class="btn sub">部署編集</a>
      <a href="addassign.php" class="btn main">配属先編集</a>
      <a href="team.php" class="btn return">一覧へ戻る</a>
    </div>

    <!-- 勤怠データ -->
    <table class="attendance">

      <tr class="time_data_title">
        <td>部署ID</td>
        <td>配属先名</td>
        <td>配属先ID</td>
        <td>配属先人数</td>
        <td></td>
      </tr>

      <!-- 登録フォーム -->
      <tr class="">
        <form action="addassign.php" method="post">
        <td>
          <select name="department_id">
            <option value="<?php if(isset($result_assign_only)) echo "$result_assign_only[department_id]"; ?>">
              <?php if(isset($result_assign_only)) echo "$result_assign_only[department_name]"; ?>
            </option>
          <?php while($row = $result_depart->fetch()): ?>
            <option value="<?=$row['department_id']?>">
              <?=$row['department_name']?>
            </option>
          <?php endwhile; ?>
          </select>
        </td>
        <td><input type="text" name="assignment_name" value="<?php if(isset($result_assign_only)) echo "$result_assign_only[assignment_name]"; ?>"></td>
        <td><input type="text" name="assignment_id" value="<?php if(isset($result_assign_only)) echo "$result_assign_only[assignment_id]"; ?>"></td>
        <td><?php if(isset($result_assign_only)) echo "$result_assign_only[count]"; ?></td>
        <!-- 登録か変更かの条件分岐 -->
        <?php if(isset($_POST['edit_assign'])):?>
        <input type="hidden" name="old_assignment_id" value="<?=$result_assign_only['assignment_id']?>">
        <input type="hidden" name="edit_assign_c" value="true">
        <?php else:?>
        <input type="hidden" name="add" value="true">
        <?php endif;?>

        <td>
          <div class="p_center">
          <input type="submit" value="登録" onClick="if(!confirm('こちらの内容で変更致しますか？')) return false;">
        </form>

          <?php if(isset($_POST['edit_assign'])):?>
          <form action="addassign.php" method="post">
          <input type="submit" value="戻る">
          </form>
          <?php endif;?>
        </div>
        </td>

      </tr>

      <!-- 編集ボタンが押された時は下記を非表示 -->
      <?php if(!isset($_POST['edit_assign'])):?>
      <?php while($row = $result_assign->fetch()): ?>
      <tr class="time_data">
        <td><?=$row['department_name']?></td>
        <td><?=$row['assignment_name']?></td>
        <td><?=$row['assignment_id']?></td>
        <td><?=$row['count']?></td>
        <td>
          <div class="p_center">
            <!-- 編集　POST送信ボタン -->
            <form action="addassign.php" method="post">
              <input type="hidden" name="assignment_id" value="<?=$row['assignment_id']?>">
              <input type="hidden" name="edit_assign" value="true">
              <input type="submit" value="編集">
            </form>

            <!-- 削除　POST送信ボタン -->
            <form action="addassign.php" method="post">
              <input type="hidden" name="assignment_id" value="<?=$row['assignment_id']?>">
              <input type="hidden" name="dlt_assign" value="true">
              <input type="submit" value="削除"
              onClick="if(!confirm('<?=$row['assignment_name']?>を削除致しますか？')) return false;"
              <?php if($row['count'] > 0) echo"disabled"?>
              >
            </form>
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
