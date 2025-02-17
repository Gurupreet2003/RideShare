<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="./style/regiseteration.css">
  <link rel="stylesheet" href="./style/regiseteration_mobile.css">
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
    <form action="../server/register.php" method="post">
      <div class="main-text">Register</div>

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

      <div class="input-container">
        <label for="name">Name</label>
        <input
          type="text"
          class="cred-input"
          name="name"
          id="name"
          autocomplete="name"
          required>
      </div>

      <div class="input-container">
        <label for="branch">Branch</label>
        <input
          type="text"
          class="cred-input"
          name="branch"
          id="branch"
          autocomplete="organization-title"
          required>
      </div>

      <div class="input-container">
        <label for="course">Course</label>
        <input
          type="text"
          class="cred-input"
          name="course"
          id="course"
          autocomplete="organization"
          required>
      </div>

      <div class="btn-container">
        <button type="submit">Register</button>
        <button type="reset">Reset</button>
        <button class="back-btn" onclick="window.location.href = '../index.php'">Back</button>
      </div>

    </form>
  </main>

</body>

</html>