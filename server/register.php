<?php
session_start();
require_once('db_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (
    empty($_POST['username']) ||
    empty($_POST['password']) ||
    empty($_POST['name']) ||
    empty($_POST['branch']) ||
    empty($_POST['course'])
  ) {
    $_SESSION['error'] = 101;
    header("location: ..\\client\\registration.php");
    exit();
  }

  $username = $_POST['username'];
  $password = $_POST['password'];
  $name = $_POST['name'];
  $branch = $_POST['branch'];
  $course = $_POST['course'];

  if (!preg_match('/^\d{7,8}$/', $username)) {
    $_SESSION['error'] = 102;
    header("location: ..\\client\\registration.php");
    exit();
  }

  if (!preg_match('/^\d{4}$/', $password)) {
    $_SESSION['error'] = 103;
    header("location: ..\\client\\registration.php");
    exit();
  }

  if (
    !preg_match('/^[a-zA-Z\s]{1,49}$/', $name) ||
    !preg_match('/^[a-zA-Z\s]{1,49}$/', $branch) ||
    !preg_match('/^[a-zA-Z\s]{1,49}$/', $course)
  ) {
    $_SESSION['error'] = 105;
    header("location: ..\\client\\registration.php");
    exit();
  }

  $hashed_password = password_hash($password, PASSWORD_DEFAULT);

  $conn = connect();
  
  $query = "SELECT COUNT(*) FROM credentials WHERE username = ?";
  $stmt = $conn->prepare($query);
  if ($stmt === false) {
    $_SESSION['error'] = 104;
    header("location: ..\\client\\registration.php");
    exit();
  }

  $stmt->bind_param('s', $username);
  $stmt->execute();
  $stmt->bind_result($count);
  $stmt->fetch();
  $stmt->close();

  if ($count > 0) {
    $_SESSION['error'] = 106;
    header("location: ..\\client\\registration.php");
    exit();
  }

  $query1 = "INSERT INTO credentials (username, password) VALUES (?, ?)";
  $query2 = "INSERT INTO user_info (username, name, branch, course) VALUES (?, ?, ?, ?)";
  
  $stmt1 = $conn->prepare($query1);
  $stmt2 = $conn->prepare($query2);
  
  if (
    $stmt1 === false ||
    $stmt2 === false
  ) {
    $_SESSION['error'] = 104;
    header("location: ..\\client\\registration.php");
    exit();
  }

  $stmt1->bind_param('ss', $username, $hashed_password);
  $stmt2->bind_param('ssss', $username, $name, $branch, $course);
  
  if ($stmt1->execute()) {
    $stmt1->close();

    $stmt2->execute();    
    $stmt2->close();
    
    $_SESSION['username'] = $username;
    $_SESSION['h_password'] = $hashed_password;
    header("location: ..\\client\\home.php");
    exit();
  } else {
    $_SESSION['error'] = 100;
    header("location: ..\\client\\registration.php");
    exit();
  }

  $conn->close();
}
?>
