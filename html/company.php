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
<title>社員専用サイト 会社情報</title>

<link rel="stylesheet" type="text/css" href="../css/base.css?v=2">
<link rel="stylesheet" type="text/css" href="../css/company.css?v=2">

</head>
<body>
  <!-- ↓ヘッダー↓ -->
  <?php require("../object/header.php"); ?>
  <!-- ↑ヘッダー↑ -->

  <main>
    <p>ここに会社情報を記載</p>
    <p>主な記載内容は福利厚生や各種手当て、</p>
    <p>またはその連絡先、相談先。</p>
    <p>その他、業務規則や就業規則など</p>
    <p>会社の内部の情報を掲載</p>
  </main>

  <?php require("../object/footer.php"); ?>
</body>
</html>
