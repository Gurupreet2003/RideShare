<?php
$error_map = [
  100 => "Registration failed.",
  101 => "No username or password provided.",
  102 => "Wrong username format.",
  103 => "Wrong password format.",
  104 => "Error preparing statement.",
  105 => "Other credential exceeded length or of invalid format.",
  106 => "Username already exsist.",
  107 => "Invalid credentials."
];
function renderMessageBox($title, $message) {
  echo <<<HTML
<link rel="stylesheet" href="../resources/messagebox.css">
<div class="msg-box-container" onclick="this.remove()">
  <div class="msg-box">
    <div class="title-bar">
      $title
    </div>
    <div class="msg-box-body">
      <div class="msg-box-text">
        $message
      </div>
      <button onclick="this.parentElement.parentElement.parentElement.remove()">OK</button>
    </div>
  </div>
</div>
HTML;
}
