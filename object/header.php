<header id="nav">
  <a class="user_box" href="mypage.php">
    <p>ユーザー情報</p>
    <p class="myname"><?php
    if(isset($_SESSION['User'])){
      echo $_SESSION['User']['name']."様";
    }
    ?></p></a>
  <ul class="flex">
    <a href="index.php"><li>TopPage</li></a>
    <a href="team.php"><li>Team</li></a>
    <a href="company.php"><li>Company</li></a>
    <a href="#"><li>業務資料</li></a>
    <a href="attendance.php"><li>勤怠管理</li></a>
    <a href="mypage.php"><li>MyPage</li></a>
    <a href="login.php?logout=1"><li class="logout">LOGOUT</li></a>
  </ul>
</header>
