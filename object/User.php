<?php
require_once "DB.php";

class User extends DB{


  // ログイン
  public function login($arr){
    $sql = 'SELECT * FROM users WHERE email = :email';
    $stmt = $this->connect->prepare($sql);
    $params = array(':email'=>$arr['email']);
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result;
  }

  // パスワードリセット　編集
  public function passReset($arr){
      $sql = "UPDATE users SET
      password = :password
      WHERE email = :email";
      $stmt = $this->connect->prepare($sql);
      $hash_pass = password_hash($arr['password'], PASSWORD_DEFAULT);
      $params = array(
        ':password'=>$hash_pass,
        ':email'=>$arr['email']
      );
      $stmt->execute($params);
  }

  // メール送信
  public function mailsend($arr){
    mb_language("Japanese");
    mb_internal_encoding("UTF-8");
    $to = $arr['email'];
    $title = '新しいパスワードをコピーしログイン時に貼り付けて下さい。';
    $message = $arr['password'];
    $header = 'From: nobu.volleyball@gmail.com';
    mb_send_mail($to, $title, $message, $header, '-f' . 'nobu.volleyball@gmail.com');
  }

  // mypage用　変数代入
  public function IdYearMonth($arr){
      $result = ["id" => $arr['id'], "year" => date('Y'), "month" => date('m')];
      return $result;
  }

  // 勤怠管理ページ用　変数代入
  public function IdDay($arr){
      $result = ["id" => $arr['id'], "edit_attendance" => date('Y-m-d')];
      return $result;
  }

  // 部署テーブル参照
  public function findDepart(){
      $sql = "SELECT
      d.department_id, department_name, count(assignment_id) as count
      FROM department d
      LEFT JOIN assignment a
      ON a.department_id = d.department_id
      GROUP BY d.department_id";
      $result = $this->connect->query($sql);
      return $result;
  }

  // 部署テーブル参照（条件付き１レコード）
  public function findDepartOnly($arr){
    $sql = 'SELECT
    d.department_id, department_name, count(assignment_id) as count
    FROM department d
    LEFT JOIN assignment a
    ON a.department_id = d.department_id
    WHERE d.department_id = :department_id
    GROUP BY d.department_id';
    $stmt = $this->connect->prepare($sql);
    $params = array(':department_id'=>$arr['department_id']);
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result;
  }

  // 部署レコード　登録
  public function addDepart($arr){
    $sql = "INSERT INTO department(department_id, department_name)
    VALUES(:department_id, :department_name)";
    $stmt = $this->connect->prepare($sql);
    $params = array(
      ':department_id'=>$arr['department_id'],
      ':department_name'=>$arr['department_name']
    );
    $stmt->execute($params);
  }

  // 部署レコード　編集
  public function editDepart($arr){
      $sql = "UPDATE department SET
      department_id = :department_id,
      department_name = :department_name
      WHERE department_id = :old_department_id";
      $stmt = $this->connect->prepare($sql);
      $params = array(
        ':department_id'=>$arr['department_id'],
        ':old_department_id'=>$arr['old_department_id'],
        ':department_name'=>$arr['department_name']
      );
      $stmt->execute($params);
  }

  // 部署レコード　削除
  public function deleteDepart($arr = null){
    if(isset($arr)){
      $sql = "DELETE FROM department WHERE department_id = :department_id";
      $stmt = $this->connect->prepare($sql);
      $params = array(':department_id'=>$arr['department_id']);
      $stmt->execute($params);
    }
  }

  // 配属先テーブル参照
  public function findAssign($arr){
    $sql = 'SELECT * FROM assignment
    WHERE department_id = :department_id';
    $stmt = $this->connect->prepare($sql);
    $params = array(':department_id'=>$arr['department_id']);
    $stmt->execute($params);
    $result = $stmt;
    return $result;
  }

  // 部署ー配属先テーブル参照
  public function find_D_A(){
    $sql = 'SELECT * FROM assignment
    JOIN department
    ON assignment.department_id = department.department_id';
    $result = $this->connect->query($sql);
    return $result;
  }

  // ここから

  // 配属先テーブル参照　配属先ーユーザー
  public function find_A_U(){
      $sql = "SELECT
      department_name, a.department_id, assignment_name, a.assignment_id, count(id) as count
      FROM assignment a
      LEFT JOIN users u
      ON a.assignment_id = u.assignment_id
      LEFT JOIN department d
      ON a.department_id = d.department_id
      GROUP BY a.assignment_id";
      $result = $this->connect->query($sql);
      return $result;
  }

  // 部署テーブル参照（条件付き１レコード）
  public function findAssignOnly($arr){
    $sql = "SELECT
    department_name, a.department_id, assignment_name, a.assignment_id, count(id) as count
    FROM assignment a
    LEFT JOIN users u
    ON a.assignment_id = u.assignment_id
    LEFT JOIN department d
    ON a.department_id = d.department_id
    WHERE a.assignment_id = :assignment_id
    GROUP BY a.assignment_id";
    $stmt = $this->connect->prepare($sql);
    $params = array(':assignment_id'=>$arr['assignment_id']);
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result;
  }

  // 部署レコード　登録
  public function addAssign($arr){
    $sql = "INSERT INTO assignment(department_id, assignment_name, assignment_id)
    VALUES(:department_id, :assignment_name, :assignment_id)";
    $stmt = $this->connect->prepare($sql);
    $params = array(
      ':department_id'=>$arr['department_id'],
      ':assignment_name'=>$arr['assignment_name'],
      ':assignment_id'=>$arr['assignment_id']
    );
    $stmt->execute($params);
  }

  // 部署レコード　編集
  public function editAssign($arr){
      $sql = "UPDATE assignment SET
      department_id = :department_id,
      assignment_name = :assignment_name,
      assignment_id = :assignment_id
      WHERE assignment_id = :old_assignment_id";
      $stmt = $this->connect->prepare($sql);
      $params = array(
        ':department_id'=>$arr['department_id'],
        ':assignment_name'=>$arr['assignment_name'],
        ':assignment_id'=>$arr['assignment_id'],
        ':old_assignment_id'=>$arr['old_assignment_id']
      );
      $stmt->execute($params);
  }

  // 部署レコード　削除
  public function deleteAssign($arr = null){
    if(isset($arr)){
      $sql = "DELETE FROM assignment WHERE assignment_id = :assignment_id";
      $stmt = $this->connect->prepare($sql);
      $params = array(':assignment_id'=>$arr['assignment_id']);
      $stmt->execute($params);
    }
  }
// ここまで

  // 勤続ステータステーブル参照
  public function findUserStatus(){
    $sql = 'SELECT * FROM user_status';
    $result = $this->connect->query($sql);
    return $result;
  }

  // 管理者ステータステーブル参照
  public function findRole(){
    $sql = 'SELECT * FROM role';
    $result = $this->connect->query($sql);
    return $result;
  }


  // 社員テーブル　配属先IDより参照
  public function findUsers($arr){
    $sql = "SELECT * FROM users
    JOIN assignment
    ON users.assignment_id = assignment.assignment_id
    JOIN user_status
    ON users.status = user_status.status_id
    WHERE users.assignment_id = :assignment_id
    ORDER BY id DESC";
    $stmt = $this->connect->prepare($sql);
    $params = array(':assignment_id'=>$arr['assignment_id']);
    $stmt->execute($params);
    $result = $stmt;
    return $result;
  }

  // 社員テーブル参照　ALL
    public function findUsersAll(){
      $sql = "SELECT * FROM users
      JOIN assignment
      ON users.assignment_id = assignment.assignment_id
      JOIN user_status
      ON users.status = user_status.status_id
      ORDER BY id DESC";
      $result = $this->connect->query($sql);
      return $result;
    }

  // 社員詳細情報　参照
  public function findUserOnly($arr){
    $sql = "SELECT *
    ,TIMESTAMPDIFF(YEAR, birthday, CURDATE()) AS age
    ,TIMESTAMPDIFF(YEAR, join_day, CURDATE()) AS join_age
    FROM users
    JOIN assignment
    ON users.assignment_id = assignment.assignment_id
    JOIN user_status
    ON users.status = user_status.status_id
    JOIN department
    ON department.department_id = assignment.department_id
    JOIN role
    ON users.role = role.role_id
    WHERE users.id = :id";
    $stmt = $this->connect->prepare($sql);
    $params = array(':id'=>$arr['id']);
    $stmt->execute($params);
    $result = $stmt;
    return $result;
  }

  // ユーザーレコード　編集
  public function editUser($arr){
      $sql = "UPDATE users SET
      assignment_id = :assignment_id,
      status = :status,
      password = :password,
      position = :position,
      name = :name,
      email = :email,
      tell = :tell,
      str_address = :str_address,
      salary = :salary,
      role = :role,
      join_day = :join_day,
      birthday = :birthday,
      user_update = :user_update
      WHERE id = :id";
      $stmt = $this->connect->prepare($sql);
      $hash_pass = password_hash($arr['password'], PASSWORD_DEFAULT);
      $params = array(
        ':assignment_id'=>$arr['assignment_id'],
        ':status'=>$arr['status'],
        ':password'=>$hash_pass,
        ':position'=>$arr['position'],
        ':name'=>$arr['name'],
        ':email'=>$arr['email'],
        ':tell'=>$arr['tell'],
        ':str_address'=>$arr['str_address'],
        ':salary'=>$arr['salary'],
        ':role'=>$arr['role'],
        ':join_day'=>$arr['join_day'],
        ':birthday'=>$arr['birthday'],
        ':user_update'=>date("Y-m-d H:i:s"),
        ':id'=>$arr['id']
      );
      $stmt->execute($params);
}

// ユーザーレコード（mypage）　編集
public function editMypage($arr){
    $sql = "UPDATE users SET
    password = :password,
    email = :email,
    tell = :tell,
    str_address = :str_address,
    user_update = :user_update
    WHERE id = :id";
    $stmt = $this->connect->prepare($sql);
    $hash_pass = password_hash($arr['password'], PASSWORD_DEFAULT);
    $params = array(
      ':password'=>$hash_pass,
      ':email'=>$arr['email'],
      ':tell'=>$arr['tell'],
      ':str_address'=>$arr['str_address'],
      ':user_update'=>date("Y-m-d H:i:s"),
      ':id'=>$arr['id']
    );
    $stmt->execute($params);
}

// ユーザーレコード　登録
public function addUser($arr){
    $sql = "INSERT INTO users(
      assignment_id, status, password, position, name, email,
      tell, str_address, salary, role, join_day, birthday
    )VALUES(
      :assignment_id, :status, :password, :position, :name, :email,
      :tell, :str_address, :salary, :role, :join_day, :birthday
    )";
    $stmt = $this->connect->prepare($sql);
    $hash_pass = password_hash($arr['password'], PASSWORD_DEFAULT);
    $params = array(
      ':assignment_id'=>$arr['assignment_id'],
      ':status'=>$arr['status'],
      ':password'=>$hash_pass,
      ':position'=>$arr['position'],
      ':name'=>$arr['name'],
      ':email'=>$arr['email'],
      ':tell'=>$arr['tell'],
      ':str_address'=>$arr['str_address'],
      ':salary'=>$arr['salary'],
      ':role'=>$arr['role'],
      ':join_day'=>$arr['join_day'],
      ':birthday'=>$arr['birthday']
    );
    $stmt->execute($params);
}

  // 勤怠テーブル参照
  public function findAttendance($arr){
    $sql = 'SELECT * FROM attendance
    WHERE user_id = :id
    AND DATE_FORMAT(day, "%Y%m") = :attendance_year_month
    ORDER BY day DESC';
    $stmt = $this->connect->prepare($sql);
    $params = array(':id'=>$arr['id'], ':attendance_year_month'=>$arr['year'].sprintf('%02d', $arr['month']) );
    $stmt->execute($params);
    $result = $stmt;
    return $result;
  }

  // 勤怠テーブル参照（条件付き１レコード）
  public function findAttendanceOnly($arr){
    $sql = 'SELECT * FROM attendance
    WHERE user_id = :id
    AND day = :day';
    $stmt = $this->connect->prepare($sql);
    $params = array(':id'=>$arr['id'], ':day'=>$arr['edit_attendance']);
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result;
  }

  // 勤怠テーブル(合計)参照
  public function findAttendanceSum($arr){
    $sql = 'SELECT count(day) as day_sum,
    TIMEDIFF(sum(out_time),sum(in_time)) as all_sum,
    sum(break_time) as break_sum,
    TIMEDIFF(TIMEDIFF(sum(out_time),sum(in_time)), SEC_TO_TIME(sum(break_time)*60)) as net_sum
    FROM attendance
    WHERE user_id = :id
    AND DATE_FORMAT(day, "%Y%m") =:attendance_year_month
    GROUP BY user_id';
    $stmt = $this->connect->prepare($sql);
    $params = array(':id'=>$arr['id'], ':attendance_year_month'=>$arr['year'].sprintf('%02d', $arr['month']) );
    $stmt->execute($params);
    $result = $stmt;
    return $result;
  }

  // 勤怠レコード　削除
  public function deleteAttendance($arr = null){
    if(isset($arr)){
      $sql = "DELETE FROM attendance WHERE user_id = :id AND day = :dlt_day";
      $stmt = $this->connect->prepare($sql);
      $params = array(':id'=>$arr['id'], ':dlt_day'=>$arr['dlt_day']);
      $stmt->execute($params);
    }
  }

  // 勤怠レコード　編集
  public function editAttendance($arr){
      $sql = "UPDATE attendance SET
      day = :day,
      in_time = :in_time,
      out_time = :out_time,
      break_time = :break_time
      WHERE user_id = :id
      AND day = :old_day";
      $stmt = $this->connect->prepare($sql);
      $params = array(
        ':day'=>$arr['day'],
        ':in_time'=>$arr['in_time'],
        ':out_time'=>$arr['out_time'],
        ':break_time'=>$arr['break_time'],
        ':id'=>$arr['id'],
        ':old_day'=>$arr['old_day']
      );
      $stmt->execute($params);
  }

  // 勤怠レコード　登録
  public function addAttendance($arr){
    $sql = "INSERT INTO attendance(day, user_id, in_time, out_time, break_time)
    VALUES(:day, :user_id, :in_time, :out_time, :break_time)";
    $stmt = $this->connect->prepare($sql);
    $params = array(
      ':day'=>$arr['day'],
      ':user_id'=>$arr['id'],
      ':in_time'=>$arr['in_time'],
      ':out_time'=>$arr['out_time'],
      ':break_time'=>$arr['break_time']
    );
    $stmt->execute($params);
  }

  // 勤怠レコード　出勤登録
  public function addAttendanceIn($arr){
    $sql = "INSERT INTO attendance(day, user_id, in_time)
    VALUES(:day, :user_id, :in_time)";
    $stmt = $this->connect->prepare($sql);
    $params = array(
      ':day'=>$arr['day'],
      ':user_id'=>$arr['id'],
      ':in_time'=>$arr['in_time']
    );
    $stmt->execute($params);
  }

  // 勤怠レコード　退勤登録
  public function editAttendanceOut($arr){
      $sql = "UPDATE attendance SET
      out_time = :out_time
      WHERE user_id = :id
      AND day = :day";
      $stmt = $this->connect->prepare($sql);
      $params = array(
        ':day'=>$arr['day'],
        ':out_time'=>$arr['out_time'],
        ':id'=>$arr['id']
      );
      $stmt->execute($params);
  }

  // 勤怠レコード　休憩登録
  public function editAttendanceBreak($arr){
      $sql = "UPDATE attendance SET
      break_time = :break_time
      WHERE user_id = :id
      AND day = :day";
      $stmt = $this->connect->prepare($sql);
      $params = array(
        ':day'=>$arr['day'],
        ':break_time'=>$arr['break_time'],
        ':id'=>$arr['id']
      );
      $stmt->execute($params);
  }

}
