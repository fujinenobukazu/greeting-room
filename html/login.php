<?php
session_start();

require_once "../object/config.php";
require_once "../object/User.php";

try{
  // DB接続
  $User = new User($host, $dbname, $user, $pass);
  $User->connectDb();

  // ログアウト
  if(isset($_GET['logout'])){
    $_SESSION = array();
  }

  // ログインチェック
  if($_POST){
    $result = $User->login($_POST);
    if(!empty($result)){
      if(password_verify($_POST['password'], $result['password'])){

        $_SESSION['User'] = $result;
        header('Location: /creative_php/html/index.php');
        exit;

      // パスワードが一致しなかった時
      }else{
        $message = "パスワードが一致しませんでした。";
      }
    // emailが一致しなかった時
    }else{
      $message = "ユーザー情報が確認できませんでした。";
    }
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
<title>社員専用サイト</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="../css/bootstrap.css">
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/bootstrap.js"></script>

<link rel="stylesheet" type="text/css" href="../css/login.css?v=2">


</head>
<body>

<section>
  <div class="main">
    <div class="center">
      <p class="title text-primary">社員専用サイト</p>
      <form action="" method="post">
      <table class="table">
        <tr class="form-group">
          <th><label for="email">Email</label></th>
          <td><input class="form-control" type="text" name="email" value=""></td>
        </tr>
        <tr class="form-group">
          <th><label for="password">password</label></th>
          <td><input class="form-control" type="password" name="password" value=""></td>
        </tr>
      </table>
      <?php if(isset($message)) echo "<p class='error'>".$message."</p>" ?>
      <a href="password_re.php"><p class="passerror">---パスワードを忘れた方はこちら---</p></a>
      <input type="submit" value="ログイン" class="btn btn-primary">
      </form>
    </div>
  </div>
</section>

</body>
</html>
