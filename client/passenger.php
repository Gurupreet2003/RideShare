<?php
session_start();
if (isset($_SESSION["username"])) {
  require_once("../server/user_verify.php");
  if (verify_user($_SESSION["username"], $_SESSION["password"])) {
    $_SESSION['user_type'] = 'passenger';
  } else {
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
  <title>Passenger Dashboard</title>
  <link rel="stylesheet" href="./style/rider.css">
</head>

<body>
  <main>
    <div id="map"></div>
    <div class="notification-bar">Welcome to 
      Ride<span style="font-family:'Dancing Script', serif;">Share</span>,
      Passenger</div>
    <div class="enex-pool-contianer">
      <button class="enex-pool-btn">Ride Request</button>
    </div>
  </main>
</body>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB8AI4Y3SN58os6Jyku6LKCRPNWb5cRnaA" async defer></script>
</script>
<script><?php
echo "let id = {$_SESSION['username']};";
?></script>
<script src="./script/test.js"></script>
<script type="module" src="./script/map.js"></script>
<script type="module" src="./script/passenger_main.js"></script>
</html>