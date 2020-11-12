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

  if(isset($_POST)){
    // 社員テーブル参照
    $result_u = $User->findUserOnly($_SESSION['User']);
    $result_u = $result_u->fetch();
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
<title>社員専用サイト MyPage編集</title>

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
      <form action="mypage.php" method="post">
      <input type="hidden" name="id" value="<?=$result_u['id']?>">
      <input type="hidden" name="edit_user" value="true">

      <tr>

        <th>配属先名</th>
        <td><?=$result_u['department_name']."ー".$result_u['assignment_name']?></td>

        <th rowspan="2">役職</th>
        <td rowspan="2" class="w20"><?=$result_u['position']?></td>

        <th rowspan="2">名前</th>
        <td rowspan="2"><?=$result_u['name']?></td>
      </tr>
      <tr>
        <th>ステータス</th>
        <td class="w20"><?=$result_u['status']?></td>
      </tr>
    </table>

    <table>
      <tr>
        <th>Email</th>
        <td><input type="text" name="email" value="<?=$result_u['email']?>"></td>

        <th>入社年月日</th>
        <td><?=$result_u['join_day']?></td>
      </tr>
      <tr>
        <th>Tell</th>
        <td><input type="text" name="tell" value="<?=$result_u['tell']?>"></td>

        <th>勤続年数</th>
        <td><?=$result_u['join_age']?>年</td>
      </tr>
      <tr>
        <th>住所</th>
        <td><input type="text" name="str_address" value="<?=$result_u['str_address']?>"></td>
        <th>生年月日</th>
        <td><?=$result_u['birthday']?></td>
      </tr>
      <tr>
        <th>給与</th>
        <td><?=$result_u['salary']?></td>
        <th>年齢</th>
        <td><?=$result_u['age']?>歳</td>
      </tr>
    </table>

    <table class="hidden">
      <tr class="top_hidden">
        <th colspan="2">管理者権限</th>
        <th colspan="2">パスワード</th>
      </tr>

      <tr>
        <th>--</th>
        <td><?=$result_u['role_name']?></td>
        <th>新しいパスワード</th>
        <td><input type="text" name="password" value=""></td>
      </tr>
    </table>
    <div class="link_btn flexbox">
      <input type="submit" value="決定" class="edit_btn decide"onClick="if(!confirm('こちらの内容で変更しますか？')) return false;">
      </form>
      <!-- 戻るボタン -->
      <form action="mypage.php" method="post">
      <input type="hidden" name="id" value="<?=$result_u['id']?>">
      <input type="submit" value="戻る" class="edit_btn return">
      </form>
    </div>
  </main>
  <!-- ↓フッター↓ -->
  <?php require("../object/footer.php"); ?>
  <!-- ↑フッター↑ -->
</body>
</html>
