<?php
declare(strict_types=1);

header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/../config/dbconnect.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  http_response_code(405);
  echo json_encode(["error" => "Method not allowed"]);
  exit;
}

$data = json_decode(file_get_contents("php://input"), true) ?? [];
$email = trim((string)($data["email"] ?? ""));
$password = (string)($data["password"] ?? "");

if ($email === "" || $password === "") {
  http_response_code(400);
  echo json_encode(["error" => "Email and password are required"]);
  exit;
}

// Cookie settings for the session cookie 
$secure = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off");
session_set_cookie_params([
  "lifetime" => 0,
  "path" => "/",
  "secure" => $secure,
  "httponly" => true,
  "samesite" => "Lax",
]); 

session_start();

try {
  $stmt = $pdo->prepare("SELECT id, email, password_hash, role_id FROM users WHERE email = ? LIMIT 1");
  $stmt->execute([$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$user || !password_verify($password, (string)$user["password_hash"])) { // verify hash 
    http_response_code(401);
    echo json_encode(["error" => "Invalid credentials"]);
    exit;
  }

  //change session id after login to reduce fixation risk
  session_regenerate_id(true);

  $_SESSION["user_id"] = (int)$user["id"];
  $_SESSION["role_id"] = (int)$user["role_id"];
  $_SESSION["email"] = (string)$user["email"];

  echo json_encode([
    "message" => "Login successful",
    "user" => [
      "id" => (int)$user["id"],
      "email" => (string)$user["email"],
      "role_id" => (int)$user["role_id"],
    ]
  ]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(["error" => "Server error"]);
}
