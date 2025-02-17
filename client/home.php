<?php
session_start();
if (isset($_SESSION["username"])) {
  require_once("../server/user_verify.php");
  if(!verify_user($_SESSION["username"], $_SESSION["password"])) {
    header("location: ..\\server\\logout.php");
  }
} else {
  header("location: ..\\index.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Selection</title>
  <link rel="stylesheet" href="./style/home.css">
</head>

<body>
  <main>
    <h2>Select User Type</h2>
    <div class="btn-container">
      <button class="main-btn" onclick="window.location.href='./rider.php'">Rider</button>
      <button class="main-btn" onclick="window.location.href='./passenger.php'">Passenger</button>
    </div>
    <button class="logout-btn" onclick="window.location.href='../server/logout.php'">logout</button>
  </main>
</body>

</html>