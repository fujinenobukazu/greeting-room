<?php
class DB{
  // プロパティ
  private $host;
  private $user;
  private $pass;
  private $dbname;
  protected $connect;

  // コンストラクタ
  function __construct($host, $dbname, $user, $pass){
  $this->host = $host;
  $this->dbname = $dbname;
  $this->user = $user;
  $this->pass = $pass;
  }

  // メソッド
  public function connectDb(){
    $this->connect = new PDO('mysql:host='.$this->host.';dbname='.$this->dbname, $this->user, $this->pass);
    // エラーチェック
    if(!$this->connect) {
      echo $this->connect->connect_errno . ' : ' . $this->connect->connect_error;
      die();
    }
  }
}
