<?php
require_once __DIR__ . "/../config/config.php";

$errors = [];

// Default signup role for this DB (recommended: parent)
// If you want new signups to be "user" instead, change to 'user'.
$defaultRoleName = 'parent';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get values from form
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';

    // Validation
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

    if (empty($errors)) {
        // Check if email already exists
        $stmt = $pdo->prepare('SELECT user_id FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            $errors[] = 'An account with that email already exists.';
        } else {
            // Split full_name into first + last
            $parts = preg_split('/\s+/', $full_name, 2);
            $first_name = $parts[0] ?? '';
            $last_name  = $parts[1] ?? '';

            if ($first_name === '') {
                $errors[] = 'Full name is required.';
            } else {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                try {
                    $pdo->beginTransaction();

                    // 1) Insert into users table (matches your DB schema)
                    $insert = $pdo->prepare(
                        'INSERT INTO users (email, password_hash, first_name, last_name, phone)
                         VALUES (:email, :password_hash, :first_name, :last_name, :phone)'
                    );
                    $insert->execute([
                        'email'         => $email,
                        'password_hash' => $password_hash,
                        'first_name'    => $first_name,
                        'last_name'     => $last_name !== '' ? $last_name : null,
                        'phone'         => $phone !== '' ? $phone : null,
                    ]);

                    $new_user_id = (int)$pdo->lastInsertId();

                    // 2) Assign default role (parent recommended; fallback to user if parent isn't found)
                    $roleStmt = $pdo->prepare("SELECT role_id FROM roles WHERE role_name = :role LIMIT 1");
                    $roleStmt->execute(['role' => $defaultRoleName]);
                    $role_id = (int)($roleStmt->fetchColumn() ?: 0);

                    if ($role_id <= 0 && $defaultRoleName !== 'user') {
                        // fallback to 'user' if 'parent' role doesn't exist (just in case)
                        $defaultRoleName = 'user';
                        $roleStmt->execute(['role' => $defaultRoleName]);
                        $role_id = (int)($roleStmt->fetchColumn() ?: 0);
                    }

                    if ($role_id <= 0) {
                        throw new RuntimeException("Default role not found in roles table.");
                    }

                    $link = $pdo->prepare('INSERT INTO user_roles (user_id, role_id) VALUES (:uid, :rid)');
                    $link->execute(['uid' => $new_user_id, 'rid' => $role_id]);

                    // 3) Create parent profile row (works with your schema; address/city/etc can be added later)
                    // Only do this for parent role (optional but recommended for your DB)
                    if ($defaultRoleName === 'parent') {
                        $parentIns = $pdo->prepare('INSERT INTO parents (user_id) VALUES (:uid)');
                        $parentIns->execute(['uid' => $new_user_id]);
                    }

                    $pdo->commit();

                    // 4) Log the user in
                    session_regenerate_id(true);
                    $_SESSION['user_id']   = $new_user_id;
                    $_SESSION['user_name'] = $first_name;
                    $_SESSION['roles']     = [$defaultRoleName];

                    header('Location: ../dashboard.php');
                    exit;

                } catch (Throwable $e) {
                    if ($pdo->inTransaction()) $pdo->rollBack();

                    // Duplicate email (safety)
                    if ($e instanceof PDOException && ($e->errorInfo[1] ?? null) == 1062) {
                        $errors[] = 'An account with that email already exists.';
                    } else {
                        // For debugging you can temporarily echo $e->getMessage()
                        $errors[] = 'Signup failed. Please try again.';
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sportsplay - Sign Up</title>
    <link rel="stylesheet" href="../assets/css/style.css">
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

            <form method="post" action="">
                <div class="form-group">
                    <span class="form-icon">👤</span>
                    <input type="text" name="full_name" placeholder="Enter Full Name"
                        value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <span class="form-icon">✉️</span>
                    <input type="email" name="email" placeholder="Enter Email"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
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

            <?php
            // Google login URI: go up from /auth to /website
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $base   = rtrim(dirname(dirname($_SERVER['PHP_SELF'])), '/\\'); // /sportsplay-main/website
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
                    data-text="signup_with">
                </div>
            </div>

            <p class="switch-link">Already have an account? <a href="login.php">Log in</a></p>
        </div>
    </div>

    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            input.type = input.type === "password" ? "text" : "password";
        }
    </script>
</body>

</html>
