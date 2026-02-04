<?php
require 'config.php';

$errors = [];

// If Google OAuth failed, we redirect back here with an error stored in session.
if (!empty($_SESSION['oauth_error'])) {
    $errors[] = $_SESSION['oauth_error'];
    unset($_SESSION['oauth_error']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $errors[] = 'Email and password are required.';
    } else {
        $stmt = $pdo->prepare('SELECT user_id, first_name, password_hash, is_active, is_admin, is_coach
                               FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $errors[] = 'Invalid email or password.';
        } elseif ((int)$user['is_active'] !== 1) {
            $errors[] = 'This account is disabled.';
        } else {
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
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sportsplay - Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <style>
        .google-wrap { display: flex; justify-content: center; margin: 10px 0; }
    </style>
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-left">
        <div class="logo"><span>Sportsplay</span></div>

        <h1 class="title"><span class="red">Welcome</span> Back</h1>
        <p class="subtitle">Enter your credentials to sign in</p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $e): ?>
                    <div><?php echo htmlspecialchars($e); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="login.php">
            <div class="form-group">
                <span class="form-icon">✉️</span>
                <input type="email" name="email" placeholder="Email"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <span class="form-icon">🔒</span>
                <input type="password" id="login_password" name="password" placeholder="Password" required>
                <span class="toggle-password" onclick="togglePassword('login_password')">👁️</span>
            </div>

            <button type="submit" class="btn-primary">Sign In</button>
        </form>

        <div class="or-divider"><span>or</span></div>

        <?php
            // Google Identity Services requires an absolute URL for data-login_uri.
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $base   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
            $google_login_uri = $scheme . '://' . $host . $base . '/google_login.php';
        ?>

        <div class="google-wrap">
            <div id="g_id_onload"
                 data-client_id="<?php echo htmlspecialchars($google_client_id); ?>"
                 data-login_uri="<?php echo htmlspecialchars($google_login_uri); ?>"
                 data-auto_prompt="false">
            </div>
            <div class="g_id_signin"
                 data-type="standard"
                 data-size="large"
                 data-theme="outline"
                 data-text="signin_with"
                 data-shape="pill"
                 data-logo_alignment="left">
            </div>
        </div>

        <div class="social-row">
            <button type="button" class="social-btn"><span>f</span> Facebook</button>
            <button type="button" class="social-btn"> Apple</button>
        </div>

        <p class="small-text">
            Don’t you have account? <a href="signup.php">Sign Up</a>
        </p>
    </div>

    <div class="auth-right"></div>
</div>

<script>
    function togglePassword(id) {
        const field = document.getElementById(id);
        field.type = field.type === 'password' ? 'text' : 'password';
    }
</script>
</body>
</html>
