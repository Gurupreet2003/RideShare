<?php
require_once("./db_connect.php");
require_once("./user_verify.php");
session_start();
verify_user($_SESSION['username'], $_SESSION['password']);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  $conn = connect();
  $requestType = $_POST['request_type'] ?? null;
  $userType = $_POST['user_type'] ?? null;
  $username = $_POST['username'] ?? null;

  if (!$requestType || !$userType || !$username) {
    http_response_code(400); // Bad Request
    echo json_encode(["status" => "error", "message" => "Missing parameters."]);
    exit;
  }

  $pool_table = null;
  if ($userType === "rider") {
    $pool_table = "rider_pool";
  } elseif ($userType === "passenger") {
    $pool_table = "passenger_pool";
  } else {
    http_response_code(400); // Bad Request
    echo json_encode(["status" => "error", "message" => "Invalid user type."]);
    exit;
  }

  // Check if user is already in the pool
  $query = "SELECT COUNT(*) FROM $pool_table WHERE username = ?";
  $stmt = $conn->prepare($query);
  if ($stmt === false) {
    http_response_code(500); // Internal server error
    echo json_encode(["status" => "error", "message" => "Failed to prepare statement."]);
    exit;
  }
  $stmt->bind_param('s', $username);
  $stmt->execute();
  $stmt->bind_result($count);
  $stmt->fetch();
  $stmt->close();

  if ($requestType == "add") {
    if ($count > 0) {
      http_response_code(200); // Success
      echo json_encode(["status" => "success", "message" => "user already found."]);
      exit;
    }

    // Add user to the pool
    $query = "INSERT INTO $pool_table (username) VALUES (?)";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
      http_response_code(500); // Internal server error
      echo json_encode(["status" => "error", "message" => "Failed to prepare statement."]);
      exit;
    }
    $stmt->bind_param('s', $username);
    if ($stmt->execute()) {
      http_response_code(200); // Success
      echo json_encode(["status" => "success", "message" => "User added to pool."]);
    } else {
      http_response_code(500); // Internal server error
      echo json_encode(["status" => "error", "message" => "Query execution failed.[ADD]"]);
    }
    $stmt->close();
    $conn->close();
  } else if ($requestType == "remove") {
    if ($count == 0) {
      http_response_code(200); // Success
      echo json_encode(["status" => "success", "message" => "User already removed."]);
      exit;
    }

    // Remove user from the pool
    $query = "DELETE FROM $pool_table WHERE username = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
      http_response_code(500); // Internal server error
      echo json_encode(["status" => "error", "message" => "Failed to prepare statement."]);
      exit;
    }
    $stmt->bind_param('s', $username);
    if ($stmt->execute()) {
      http_response_code(200); // Success
      echo json_encode(["status" => "success", "message" => "User Removed from the pool."]);
      exit;
    } else {
      http_response_code(500); // Internal server error
      echo json_encode(["status" => "error", "message" => "Query execution failed.[REMOVE]"]);
      exit;
    }
    $stmt->close();
    $conn->close();
  } else if ($requestType == "check_ifExist") {
    if($count > 0) {
      http_response_code(200);
      echo json_encode(["status" => "success", "message" => "exist"]);
      exit;
    } else {
      http_response_code(200);
      echo json_encode(["status" => "success", "message" => "not exist"]);
      exit;
    }
  }
} else {
  header("Location: ../index.php");
  exit;
}