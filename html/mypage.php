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



    // 勤怠テーブル削除 削除ボタンを押した場合に動作。）
    if(isset($_POST['dlt_day'])){
      $User->deleteAttendance($_POST);

    // 勤怠テーブル編集（編集ボタンを押したレコードを抽出。）
    }elseif(isset($_POST['edit_attendance'])){
      $result_a_only = $User->findAttendanceOnly($_POST);

    // 勤怠テーブル編集（編集ボタンを押した後、登録ボタンを押した場合に動作。）
    }elseif(isset($_POST['edit_attendance_c'])){
      $User->editAttendance($_POST);

    // 勤怠テーブル登録
    }elseif(isset($_POST['add'])){
      $User->addAttendance($_POST);

    // mypage編集
    }elseif(isset($_POST['edit_user'])){
      $User->editMypage($_POST);
    }

    if(!isset($_POST['year'])){// 初回アクセス時
      $result_if = $User->IdYearMonth($_SESSION['User']);
      // 勤怠テーブル参照
      $result_a = $User->findAttendance($result_if);
      // 勤怠テーブル(合計)参照
      $result_a_sum = $User->findAttendanceSum($result_if);
      $result_a_sum = $result_a_sum->fetch();
    }else{
      // 勤怠テーブル参照
      $result_a = $User->findAttendance($_POST);
      // 勤怠テーブル(合計)参照
      $result_a_sum = $User->findAttendanceSum($_POST);
      $result_a_sum = $result_a_sum->fetch();
    }

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
<title>社員専用サイト MyPage</title>

<link rel="stylesheet" type="text/css" href="../css/base.css?v=2">
<link rel="stylesheet" type="text/css" href="../css/team_employee_data.css?v=2">
<link rel="stylesheet" type="text/css" href="../css/mypage.css?v=2">
</head>
<body>
  <!-- ↓ヘッダー↓ -->
  <?php require("../object/header.php"); ?>
  <!-- ↑ヘッダー↑ -->
  <main>
    <div class="link_btn">
      <a href="team.php" class="btn return">一覧へ戻る</a>
    </div>

    <!-- 社員データ -->
    <table class="data1">
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
        <td class="w20"><?=$result_u['status_name']?></td>
      </tr>
    </table>

    <table>
      <tr>
        <th>Email</th>
        <td><?=$result_u['email']?></td>
        <th>入社年月日</th>
        <td><?=$result_u['join_day']?></td>
      </tr>
      <tr>
        <th>Tell</th>
        <td><?=$result_u['tell']?></td>
        <th>勤続年数</th>
        <td><?=$result_u['join_age']?>年</td>
      </tr>
      <tr>
        <th>住所</th>
        <td><?=$result_u['str_address']?></td>
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

    <div class="right">
      <form action="mypage_edit.php" method="post">
        <input type="hidden" name="year" value="<?=date('Y')?>">
        <input type="hidden" name="month" value="<?=date('m')?>">
        <input type="hidden" name="id" value="<?=$result_u['id']?>">
        <input type="submit" value="編集" class="edit_btn">
      </form>
    </div>

    <!-- 勤怠データ -->
    <table class="attendance">
      <tr class="day">

        <form action="mypage.php" method="post">
        <td class="text_r">

          <select name="year">
          <?php for($i = date('Y'); $i >= 1900; $i--): ?>
            <option value="<?=$i ?>"><?=$i ?></option>
          <?php endfor; ?>
          </select>

        </td>
        <td>年</td>
        <td class="text_r">

          <select name="month">
          <?php for($i = 12; $i >= 1; $i--): ?>
            <option value="<?=$i ?>" <?php if($i == date('m')) echo "selected"; ?> ><?=$i ?></option>
          <?php endfor; ?>
          </select>

        </td>
        <td>月</td>
        <input type="hidden" name="id" value="<?=$result_u['id']?>">
        <td><div class="p_center">
          <input type="submit" value="参　照">
        </div></td>
      </form>
      </tr>
      <tr class="attendance_sum">
        <td>出勤日数</td>
        <td>勤務時間</td>
        <td>休憩時間</td>
        <td>総勤務時間</td>
        <td></td>
      </tr>
      <tr class="attendance_sum text_r">
        <td><?php if($result_a_sum) echo "$result_a_sum[day_sum]"; ?></td>
        <td><?php if($result_a_sum) echo "$result_a_sum[all_sum]"; ?></td>
        <td><?php if($result_a_sum) echo "$result_a_sum[break_sum]"; ?></td>
        <td><?php if($result_a_sum) echo "$result_a_sum[net_sum]"; ?></td>
        <td></td>
      </tr>

      <tr class="time_data_title">
        <td>日付</td>
        <td>出勤時刻</td>
        <td>退勤時刻</td>
        <td>休憩時間</td>
        <td></td>
      </tr>

      <!-- 登録フォーム -->
      <tr class="">
        <form action="mypage.php" method="post">
        <td><input type="date" name="day" value="<?php if(isset($result_a_only)) echo "$result_a_only[day]"; ?>"></td>
        <td><input type="time" name="in_time" value="<?php if(isset($result_a_only)) echo "$result_a_only[in_time]"; ?>"></td>
        <td><input type="time" name="out_time" value="<?php if(isset($result_a_only)) echo "$result_a_only[out_time]"; ?>"></td>
        <td><input type="number" name="break_time" value="<?php if(isset($result_a_only)) echo "$result_a_only[break_time]"; ?>"></td>
        <!-- 登録か編集かの条件分岐 -->
        <?php if(isset($_POST['edit_attendance'])):?>
        <input type="hidden" name="old_day" value="<?php if(isset($result_a_only)) echo "$result_a_only[day]"; ?>">
        <input type="hidden" name="edit_attendance_c" value="true">
        <?php else:?>
        <input type="hidden" name="add" value="true">
        <?php endif;?>

        <!-- 表示崩れを防ぐ為の共通のPOST送信 -->
        <input type="hidden" name="year" value="<?=date('Y')?>">
        <input type="hidden" name="month" value="<?=date('m')?>">
        <input type="hidden" name="id" value="<?=$result_u['id']?>">

        <td>
          <div class="p_center">
          <input type="submit" value="登録" onClick="if(!confirm('こちらの内容で変更致しますか？')) return false;">
        </form>

          <?php if(isset($_POST['edit_attendance'])):?>
          <form action="mypage.php" method="post" class="inline">
          <input type="hidden" name="year" value="<?=date('Y')?>">
          <input type="hidden" name="month" value="<?=date('m')?>">
          <input type="hidden" name="id" value="<?=$result_u['id']?>">
          <input type="submit" value="戻る">
          </form>
          <?php endif;?>
        </div>
        </td>

      </tr>

      <!-- 編集ボタンが押された時は下記を非表示 -->
      <?php if(!isset($_POST['edit_attendance'])):?>
      <?php while($row = $result_a->fetch()): ?>
      <tr class="time_data">
        <td><?=$row['day']?></td>
        <td><?=$row['in_time']?></td>
        <td><?=$row['out_time']?></td>
        <td><?=$row['break_time']?></td>
        <td>
          <div class="p_center">
            <!-- 編集　POST送信ボタン -->
            <form action="mypage.php" method="post">
              <input type="hidden" name="year" value="<?=date('Y')?>">
              <input type="hidden" name="month" value="<?=date('m')?>">
              <input type="hidden" name="id" value="<?=$result_u['id']?>">
              <input type="hidden" name="edit_attendance" value="<?=$row['day']?>">
              <input type="submit" value="編集">
            </form>

            <!-- 削除　POST送信ボタン -->
            <form action="mypage.php" method="post">
              <input type="hidden" name="year" value="<?=date('Y')?>">
              <input type="hidden" name="month" value="<?=date('m')?>">
              <input type="hidden" name="id" value="<?=$result_u['id']?>">
              <input type="hidden" name="dlt_day" value="<?=$row['day']?>">
              <input type="submit" value="削除" onClick="if(!confirm('<?=$row['day']?>の出勤記録を削除しますか？')) return false;">
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
