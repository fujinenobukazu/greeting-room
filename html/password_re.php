<?php
require_once "../object/config.php";
require_once "../object/User.php";

try{
  // DB接続
  $User = new User($host, $dbname, $user, $pass);
  $User->connectDb();

  // ログインチェック
  if(!empty($_POST)){
    $result = $User->login($_POST);
    if(!empty($result)){
      $message = "ユーザー情報を確認できました。";
      $User->passReset($_POST);
      $User->mailsend($_POST);

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
var_dump($_POST['password']);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>社員専用サイト メインメニュー</title>

<link rel="stylesheet" type="text/css" href="../css/base.css?v=2">

</head>
<body>
  <section class="centerbox">
    <p>パスワードを忘れた方はこちらに</p>
    <p>登録のメールアドレスを記入しパスワードの再設定を行って下さい。</p>
    <form action="" method="post">
      <input type="text" name="email">
      <input type="hidden" name="password" value=
      "<?= substr(uniqid(rand(),true), 6, 6)?>">
      <input type="submit" value="送信">
    </form>
    <a href="login.php"><p>戻る</p></a>
  </section>
</body>
</html>
