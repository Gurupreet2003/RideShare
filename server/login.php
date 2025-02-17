<?php
session_start();
require_once('db_connect.php');
require_once("user_verify.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (
    empty($_POST['username']) ||
    empty($_POST['password'])
  ) {
    $_SESSION['error'] = 101;
    header("location: ..\\client\\login.php");
    exit();
  }

  $username = $_POST['username'];
  $password = $_POST['password'];

  if (verify_user($username, $password)) {
    $_SESSION['username'] = $username;
    $_SESSION['password'] = $password;
    $_SESSION['user_type'] = null;
    header("location: ..\\client\\home.php");
  } else {
    header("location: ..\\client\\login.php");
  }
}
