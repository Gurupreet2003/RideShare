<?php
require_once("db_connect.php");

function verify_user($username, $password) {
  if (!preg_match('/^\d{7,8}$/', $username)) {
    $_SESSION['error'] = 102;
    return false;
  }

  if (!preg_match('/^\d{4}$/', $password)) {
    $_SESSION['error'] = 103;
    return false;
  }

  $conn = connect();

  $query = "SELECT username, password FROM credentials WHERE username = ?";

  $stmt = $conn->prepare($query);
  if ($stmt === false) {
    $_SESSION['error'] = 104;
    return false;
  }

  $stmt->bind_param('s', $username);

  $stmt->execute();
  $stmt->bind_result($rcv_username, $rcv_password);

  if ($stmt->fetch()) {
    if (
      $username == $rcv_username &&
      password_verify($password, $rcv_password)
    ) {
      return true;
      exit();
    } else {
      $_SESSION['error'] = 107;
      return false;
    }
  } else {
    $_SESSION['error'] = 107;
    return false;
  }
  $conn->close();
}
?>