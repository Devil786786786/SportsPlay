<?php
require_once __DIR__ . "/../config/config.php";

// Google Identity Services (GIS) POST handler.
// Receives:
//  - credential (ID token JWT)
//  - g_csrf_token (must match g_csrf_token cookie)

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

function fail_and_redirect(string $message): void {
    $_SESSION['oauth_error'] = $message;
    header('Location: login.php');
    exit;
}

// --- CSRF check required by GIS ---
$csrf_cookie = $_COOKIE['g_csrf_token'] ?? '';
$csrf_body   = $_POST['g_csrf_token'] ?? '';
if ($csrf_cookie === '' || $csrf_body === '' || !hash_equals($csrf_cookie, $csrf_body)) {
    fail_and_redirect('Google sign-in failed (CSRF check). Please try again.');
}

$id_token = $_POST['credential'] ?? '';
if ($id_token === '') {
    fail_and_redirect('Google sign-in failed (missing token). Please try again.');
}

if (empty($google_client_id)) {
    fail_and_redirect('Google sign-in is not configured yet (missing Client ID in config/config.php).');
}

// --- Verify ID token ---
$payload = false;
$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;

    try {
        $client = new Google_Client(['client_id' => $google_client_id]);
        $payload = $client->verifyIdToken($id_token);
    } catch (Throwable $e) {
        $payload = false;
    }
} else {
    // Dev fallback: tokeninfo endpoint (OK for debugging)
    $url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($id_token);
    $json = @file_get_contents($url);
    if ($json !== false) {
        $data = json_decode($json, true);
        if (is_array($data) && ($data['aud'] ?? '') === $google_client_id) {
            $payload = $data;
        }
    }
}

if (!$payload || !is_array($payload)) {
    fail_and_redirect('Google sign-in failed (invalid token).');
}

$email = $payload['email'] ?? null;
$sub   = $payload['sub'] ?? null;

if (!$email || !$sub) {
    fail_and_redirect('Google sign-in failed (missing profile info).');
}

$given_name  = $payload['given_name'] ?? '';
$family_name = $payload['family_name'] ?? '';
$name        = $payload['name'] ?? '';
if ($given_name === '' && $name !== '') {
    $parts = preg_split('/\s+/', trim($name), 2);
    $given_name = $parts[0] ?? '';
    $family_name = $parts[1] ?? '';
}
if ($given_name === '') {
    $given_name = 'User';
}

$picture = $payload['picture'] ?? null;

// --- New DB schema note ---
// This project DB now uses roles + user_roles, and does NOT store google_sub/provider on users.
// We link Google accounts via an oauth_identities table.
// If you haven't created it yet, run this once in Adminer/SQL command:
//
// CREATE TABLE oauth_identities (
//   id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//   user_id INT UNSIGNED NOT NULL,
//   provider VARCHAR(30) NOT NULL,
//   provider_sub VARCHAR(128) NOT NULL,
//   created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
//   UNIQUE KEY uniq_provider_sub (provider, provider_sub),
//   KEY idx_user_id (user_id),
//   CONSTRAINT fk_oauth_user FOREIGN KEY (user_id)
//     REFERENCES users(user_id) ON DELETE CASCADE
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

try {
    // 1) Try to find existing identity by provider_sub
    $stmt = $pdo->prepare(
        "SELECT u.user_id, u.first_name, u.is_active
         FROM oauth_identities oi
         JOIN users u ON u.user_id = oi.user_id
         WHERE oi.provider = 'google' AND oi.provider_sub = :sub
         LIMIT 1"
    );
    $stmt->execute(['sub' => $sub]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    fail_and_redirect('Google sign-in needs DB setup: please create the oauth_identities table (see comment in auth/google_login.php).');
}

if ($user) {
    if ((int)$user['is_active'] !== 1) {
        fail_and_redirect('This account is disabled.');
    }

    session_regenerate_id(true);
    $_SESSION['user_id']   = (int)$user['user_id'];
    $_SESSION['user_name'] = $user['first_name'];
    $_SESSION['roles']     = get_user_roles($pdo, (int)$user['user_id']);

    redirect_after_login();
}

// 2) No identity yet: find (or create) user by email
$stmt = $pdo->prepare('SELECT user_id, first_name, is_active FROM users WHERE email = :email LIMIT 1');
$stmt->execute(['email' => $email]);
$existing = $stmt->fetch();

if ($existing) {
    if ((int)$existing['is_active'] !== 1) {
        fail_and_redirect('This account is disabled.');
    }

    // Link Google identity to the existing user account
    $ins = $pdo->prepare(
        "INSERT INTO oauth_identities (user_id, provider, provider_sub)
         VALUES (:uid, 'google', :sub)"
    );
    $ins->execute(['uid' => (int)$existing['user_id'], 'sub' => $sub]);

    session_regenerate_id(true);
    $_SESSION['user_id']   = (int)$existing['user_id'];
    $_SESSION['user_name'] = $existing['first_name'];
    $_SESSION['roles']     = get_user_roles($pdo, (int)$existing['user_id']);

    redirect_after_login();
}

// 3) Create a brand new user + role + parent profile, then link identity
$random_password = bin2hex(random_bytes(32));
$password_hash = password_hash($random_password, PASSWORD_DEFAULT);

$insUser = $pdo->prepare(
    'INSERT INTO users (email, password_hash, first_name, last_name, phone, ip_address, is_active)
     VALUES (:email, :hash, :first, :last, NULL, :ip, 1)'
);
$insUser->execute([
    'email' => $email,
    'hash'  => $password_hash,
    'first' => $given_name,
    'last'  => $family_name !== '' ? $family_name : null,
    'ip'    => $_SERVER['REMOTE_ADDR'] ?? null,
]);

$new_user_id = (int)$pdo->lastInsertId();

// Assign default role 'user'
$roleStmt = $pdo->prepare("SELECT role_id FROM roles WHERE role_name = 'user' LIMIT 1");
$roleStmt->execute();
$role_id = (int)($roleStmt->fetchColumn() ?: 0);
if ($role_id > 0) {
    $link = $pdo->prepare('INSERT INTO user_roles (user_id, role_id) VALUES (:uid, :rid)');
    $link->execute(['uid' => $new_user_id, 'rid' => $role_id]);
}

// Create parent profile row (optional but matches schema)
$parentIns = $pdo->prepare('INSERT INTO parents (user_id) VALUES (:uid)');
$parentIns->execute(['uid' => $new_user_id]);

// Link google identity
$insId = $pdo->prepare(
    "INSERT INTO oauth_identities (user_id, provider, provider_sub)
     VALUES (:uid, 'google', :sub)"
);
$insId->execute(['uid' => $new_user_id, 'sub' => $sub]);

session_regenerate_id(true);
$_SESSION['user_id']   = $new_user_id;
$_SESSION['user_name'] = $given_name;
$_SESSION['roles']     = ['user'];

header('Location: ../dashboard.php');
exit;
