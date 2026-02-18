<?php
// config.php

// Database settings for XAMPP
$db_host = 'localhost';       // localhost
$db_name = 'sportsplay';      // the DB you created in Adminer
$db_user = 'root';
$db_pass = 'sportsplay123';   // the password we set for root

// Google Sign-In (Google Identity Services)
$google_client_id = '1020968959954-jrqm4lre2si4iv0bgt3tb5qp75fjrk1c.apps.googleusercontent.com';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Default port (3306):
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";

    // If you use a custom port like 3307, use this instead:
    // $dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4";

    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    exit('Database connection failed: ' . $e->getMessage());
}

session_start();

// Base URL path for redirects (works for /sportsplay-main/website and similar subfolders)
$APP_URL_BASE = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$APP_URL_BASE = preg_replace('#/(auth|admin-dash)$#', '', $APP_URL_BASE);
if ($APP_URL_BASE === '') { $APP_URL_BASE = '/'; }

function app_url(string $path = ''): string {
    global $APP_URL_BASE;
    $path = ltrim($path, '/');
    return rtrim($APP_URL_BASE, '/') . '/' . $path;
}



// --- Auth helpers for the new roles schema (roles + user_roles) ---
function get_user_roles(PDO $pdo, int $user_id): array {
    $stmt = $pdo->prepare(
        'SELECT r.role_name
         FROM user_roles ur
         JOIN roles r ON r.role_id = ur.role_id
         WHERE ur.user_id = :uid'
    );
    $stmt->execute(['uid' => $user_id]);
    $roles = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    // Normalize to lowercase unique strings
    $roles = array_values(array_unique(array_map('strtolower', $roles)));
    return $roles;
}

function has_role(string $role): bool {
    $role = strtolower($role);
    return in_array($role, $_SESSION['roles'] ?? [], true);
}
function require_any_role(array $roles): void {
    require_login();
    foreach ($roles as $role) {
        if (has_role($role)) return;
    }
    header('Location: ' . app_url('dashboard.php'));
    exit;
}


function require_login(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . app_url('auth/login.php'));
        exit;
    }
}

function require_role(string $role): void {
    require_login();
    if (!has_role($role)) {
        header('Location: ' . app_url('dashboard.php'));
        exit;
    }
}

function redirect_after_login(): void {
    if (has_role('admin')) {
        header('Location: ' . app_url('admin-dash/admin_dashboard.php'));
    } elseif (has_role('coach')) {
        header('Location: ' . app_url('coach_dashboard.php'));
    } else {
        header('Location: ' . app_url('dashboard.php'));
    }
    exit;
}

