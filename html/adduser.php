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

  if(isset($_POST)){
    // 配属先ドロップダウン
    $result_d_a = $User->find_D_A();
    // 勤続ステータスドロップダウン
    $result_u_status = $User->findUserStatus();
    // 権限リストドロップダウン
    $result_role = $User->findRole();
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
<title>社員専用サイト 社員情報登録</title>

<link rel="stylesheet" type="text/css" href="../css/base.css?v=2">
<link rel="stylesheet" type="text/css" href="../css/team_employee_editdata.css?v=2">

</head>
<body>
  <!-- ↓ヘッダー↓ -->
  <?php require("../object/header.php"); ?>
  <!-- ↑ヘッダー↑ -->
  <main>

    <!-- 社員データ -->
    <table class="data1">
      <form action="team_employee.php?display=all" method="post">
      <input type="hidden" name="add_user" value="true">

      <tr>

        <th>配属先名</th>
        <td>
          <select name="assignment_id">
          <?php while($row = $result_d_a->fetch()): ?>
            <option value="<?=$row['assignment_id']?>">
              <?=$row['department_name']."ー".$row['assignment_name']?>
            </option>
          <?php endwhile; ?>
          </select>
        </td>

        <th rowspan="2">役職</th>
        <td rowspan="2" class="w20">
          <input type="text" name="position">
        </td>

        <th rowspan="2">名前</th>
        <td rowspan="2">
          <input type="text" name="name">

        </td>
      </tr>
      <tr>
        <th>ステータス</th>
        <td class="w20">
          <select name="status">
          <?php while($row = $result_u_status->fetch()): ?>
            <option value="<?=$row['status_id']?>">
              <?=$row['status_name']?>
            </option>
          <?php endwhile; ?>
          </select>

        </td>
      </tr>
    </table>

    <table>
      <tr>
        <th>Email</th>
        <td><input type="text" name="email"></td>

        <th>入社年月日</th>
        <td><input type="date" name="join_day"></td>
      </tr>
      <tr>
        <th>Tell</th>
        <td><input type="text" name="tell"></td>

        <th>勤続年数</th>
        <td>--年</td>
      </tr>
      <tr>
        <th>住所</th>
        <td><input type="text" name="str_address"></td>
        <th>生年月日</th>
        <td><input type="date" name="birthday"></td>
      </tr>
      <tr>
        <th>給与</th>
        <td><input type="number" name="salary"></td>
        <th>年齢</th>
        <td>--歳</td>
      </tr>
    </table>

    <table class="hidden">
      <tr class="top_hidden">
        <th colspan="2">管理者権限</th>
        <th colspan="2">パスワード</th>
      </tr>

      <tr>
        <th>
          管理者権限を選択して下さい。
        </th>
        <td>
          <select name="role">
          <?php while($row = $result_role->fetch()): ?>
            <option value="<?=$row['role_id']?>">
              <?=$row['role_name']?>
            </option>
          <?php endwhile; ?>
          </select>
        </td>
        <th>新しいパスワード</th>
        <td><input type="pasword" name="password"></td>
      </tr>
    </table>
    <div class="link_btn flexbox">
      <input type="submit" value="決定" class="edit_btn decide"onClick="if(!confirm('こちらの内容で登録しますか？')) return false;">
      </form>
      <!-- 戻るボタン -->
      <form action="team.php" method="post">
      <input type="submit" value="戻る" class="edit_btn return">
      </form>
    </div>
  </main>
  <!-- ↓フッター↓ -->
  <?php require("../object/footer.php"); ?>
  <!-- ↑フッター↑ -->
</body>
</html>
