<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>login</title>
  <link rel="stylesheet" href="./style/login.css">
  <link rel="stylesheet" href="./style/login_mobile.css">
</head>

<body>
  <?php
  session_start();
  if (isset($_SESSION['error'])) {
    
    require_once "../resources/msg-box.php";
    renderMessageBox("Error", $error_map[$_SESSION['error']]);
    
    session_unset();
    session_destroy();
  }
  ?>
  <main>
    <form action="../server/login.php" method="post">

      <div class="main-text">Login</div>
      
      <div class="input-container">
        <label for="username">Username</label>
        <input
          type="text"
          class="cred-input"
          name="username"
          id="username"
          autocomplete="username"
          required>
      </div>

      <div class="input-container">
        <label for="password">Password</label>
        <input
          type="text"
          class="cred-input"
          name="password"
          id="password"
          autocomplete="current-password"
          required>
      </div>

      <div class="btn-container">
        <button type="submit">log in</button>
        <button type="reset">Reset</button>
        <button class="back-btn" onclick="window.location.href = '../index.php'">Back</button>
      </div>

    </form>
  </main>
</body>

</html>