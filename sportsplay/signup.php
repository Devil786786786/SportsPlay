<?php
require 'config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get values from form
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';

    // Basic validation (no format check for email)
    if ($full_name === '') {
        $errors[] = 'Full name is required.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }

    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    // If no validation errors, insert into DB
    if (empty($errors)) {
        // Check if email already exists (uniqueness)
        $stmt = $pdo->prepare('SELECT user_id FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            $errors[] = 'An account with that email already exists.';
        } else {
            // Split full_name into first + last
            $parts = preg_split('/\s+/', $full_name, 2);
            $first_name = $parts[0];
            $last_name  = $parts[1] ?? '';

            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Insert into users table
            $insert = $pdo->prepare(
                'INSERT INTO users (email, password_hash, first_name, last_name, phone, ip_address)
                 VALUES (:email, :password_hash, :first_name, :last_name, :phone, :ip)'
            );
            $insert->execute([
                'email'         => $email,
                'password_hash' => $password_hash,
                'first_name'    => $first_name,
                'last_name'     => $last_name,
                'phone'         => $phone !== '' ? $phone : null,
                'ip'            => $_SERVER['REMOTE_ADDR'] ?? null,
            ]);

            // Log the user in
            session_regenerate_id(true);
            $_SESSION['user_id']   = $pdo->lastInsertId();
            $_SESSION['user_name'] = $first_name;
            $_SESSION['is_admin']  = 0;
            $_SESSION['is_coach']  = 0;

            header('Location: dashboard.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sportsplay - Sign Up</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <style>
        .google-wrap {
            display: flex;
            justify-content: center;
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <div class="auth-wrapper">
        <div class="auth-left">
            <div class="logo"><span>Sportsplay</span></div>

            <h1 class="title"><span class="red">Get</span> Started</h1>
            <p class="subtitle">Welcome to SportsPlay – Let’s create your account!</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $e): ?>
                        <div><?php echo htmlspecialchars($e); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="signup.php">
                <div class="form-group">
                    <span class="form-icon">👤</span>
                    <input type="text" name="full_name" placeholder="Enter Full Name"
                        value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <span class="form-icon">✉️</span>
                    <input type="email" name="email" placeholder="Enter Email" required>
                </div>

                <div class="form-group">
                    <span class="form-icon">📞</span>
                    <input type="text" name="phone" placeholder="Phone (optional)"
                        value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <span class="form-icon">🔒</span>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                    <span class="toggle-password" onclick="togglePassword('password')">👁️</span>
                </div>

                <div class="form-group">
                    <span class="form-icon">🔒</span>
                    <input type="password" id="confirm_password" name="confirm_password"
                        placeholder="Confirm Password" required>
                    <span class="toggle-password" onclick="togglePassword('confirm_password')">👁️</span>
                </div>

                <button type="submit" class="btn-primary">Sign Up</button>
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
                    data-text="signup_with"
                    data-shape="pill"
                    data-logo_alignment="left">
                </div>
            </div>

            <div class="social-row">
                <button type="button" class="social-btn"><span>f</span> Facebook</button>
                <button type="button" class="social-btn"> Apple</button>
            </div>

            <p class="small-text">
                Have an account? <a href="login.php">Sign In</a>
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