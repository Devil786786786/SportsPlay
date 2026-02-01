<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../config/dbconnect.php"; 

// Only allow POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

// Read input JSON
$data = json_decode(file_get_contents("php://input"), true);

// Get fields
$fullName = trim($data["full_name"] ?? "");
$email = trim($data["email"] ?? "");
$password = $data["password"] ?? "";
$confirmPassword = $data["confirm_password"] ?? "";

//Validation 

if (empty($fullName) || empty($email) || empty($password) || empty($confirmPassword)) {
    http_response_code(400);
    echo json_encode(["error" => "All fields are required"]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid email address"]);
    exit;
}

if (strlen($password) < 8) {
    http_response_code(400);
    echo json_encode(["error" => "Password must be at least 8 characters"]);
    exit;
}

if ($password !== $confirmPassword) {
    http_response_code(400);
    echo json_encode(["error" => "Passwords do not match"]);
    exit;
}


try {
    // Check duplicate email
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);

    if ($check->fetch()) {
        http_response_code(409);
        echo json_encode(["error" => "Email already exists"]);
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert user
    $stmt = $pdo->prepare(
        "INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)"
    );
    $stmt->execute([$fullName, $email, $hashedPassword]);

    http_response_code(201);
    echo json_encode([
        "message" => "User registered successfully"
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Server error"
    ]);
}
