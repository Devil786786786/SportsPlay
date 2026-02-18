<?php
require_once __DIR__ . "/../config/config.php"; 

// This endpoint is called by Google Identity Services (GIS) via HTTPS POST.
// It receives:
//  - credential (the ID token JWT)
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

if (empty($google_client_id) || $google_client_id === 'PASTE_YOUR_GOOGLE_CLIENT_ID_HERE') {
    fail_and_redirect('Google sign-in is not configured yet (missing Client ID in config.php).');
}

$payload = false;

// Preferred: verify the ID token using Google's PHP client library.
$autoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;

    try {
        $client = new Google_Client(['client_id' => $google_client_id]);
        $payload = $client->verifyIdToken($id_token);
    } catch (Throwable $e) {
        $payload = false;
    }
} else {
    // Dev fallback: tokeninfo endpoint (OK for debugging; use library for production).
    // https://developers.google.com/identity/gsi/web/guides/verify-google-id-token
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
$sub   = $payload['sub'] ?? null; // unique, stable Google Account identifier

if (!$email || !$sub) {
    fail_and_redirect('Google sign-in failed (missing profile info).');
}

$given_name  = $payload['given_name'] ?? '';
$family_name = $payload['family_name'] ?? '';
$name        = $payload['name'] ?? '';
$picture     = $payload['picture'] ?? null;

if ($given_name === '' && $name !== '') {
    // Fallback: split full name
    $parts = preg_split('/\s+/', trim($name), 2);
    $given_name = $parts[0] ?? '';
    $family_name = $parts[1] ?? '';
}
if ($given_name === '') {
    $given_name = 'User';
}

// --- Find or create account ---
try {
    $stmt = $pdo->prepare(
        'SELECT user_id, first_name, is_active, is_admin, is_coach, provider, google_sub
         FROM users
         WHERE google_sub = :sub OR email = :email
         LIMIT 1'
    );
    $stmt->execute(['sub' => $sub, 'email' => $email]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    // Most likely missing DB columns.
    fail_and_redirect('Google sign-in needs a small DB update (provider/google_sub columns). See the setup steps.');
}

if ($user) {
    if ((int)$user['is_active'] !== 1) {
        fail_and_redirect('This account is disabled.');
    }

    // Prevent account takeover: if an existing local account uses this email but isn't linked,
    // we don’t auto-link. Log in with password first, then we can add linking later.
    if (($user['provider'] ?? 'local') === 'local' && empty($user['google_sub'])) {
        fail_and_redirect('An account with this email already exists. Please sign in with password first, then we can link Google.');
    }

    // If we matched by email and google_sub is empty, link it (safe because provider isn’t local).
    if (empty($user['google_sub'])) {
        $upd = $pdo->prepare('UPDATE users SET google_sub = :sub WHERE user_id = :id');
        $upd->execute(['sub' => $sub, 'id' => $user['user_id']]);
    }

    // Update avatar (optional)
    if ($picture) {
        $upd = $pdo->prepare('UPDATE users SET avatar_url = :pic WHERE user_id = :id');
        $upd->execute(['pic' => $picture, 'id' => $user['user_id']]);
    }

    session_regenerate_id(true);
    $_SESSION['user_id']   = $user['user_id'];
    $_SESSION['user_name'] = $user['first_name'];
    $_SESSION['is_admin']  = (int)$user['is_admin'];
    $_SESSION['is_coach']  = (int)$user['is_coach'];

    if (!empty($_SESSION['is_admin'])) {
        header('Location: admin_coaches.php');
    } elseif (!empty($_SESSION['is_coach'])) {
        header('Location: coach_dashboard.php');
    } else {
        header('Location: dashboard.php');
    }
    exit;
}

// Create a new user for Google sign-in
$random_password = bin2hex(random_bytes(32));
$password_hash = password_hash($random_password, PASSWORD_DEFAULT);

$insert = $pdo->prepare(
    'INSERT INTO users (email, password_hash, first_name, last_name, ip_address, provider, google_sub, avatar_url)
     VALUES (:email, :hash, :first, :last, :ip, :provider, :sub, :avatar)'
);
$insert->execute([
    'email'    => $email,
    'hash'     => $password_hash,
    'first'    => $given_name,
    'last'     => $family_name !== '' ? $family_name : null,
    'ip'       => $_SERVER['REMOTE_ADDR'] ?? null,
    'provider' => 'google',
    'sub'      => $sub,
    'avatar'   => $picture,
]);

$user_id = (int)$pdo->lastInsertId();

session_regenerate_id(true);
$_SESSION['user_id']   = $user_id;
$_SESSION['user_name'] = $given_name;
$_SESSION['is_admin']  = 0;
$_SESSION['is_coach']  = 0;

header('Location: dashboard.php');
exit;
