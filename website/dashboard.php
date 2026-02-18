<?php
require_once __DIR__ . '/config/config.php';
require_login();

// If someone with admin/coach role lands here, send them to the right dashboard.
if (has_role('admin') || has_role('coach')) {
    redirect_after_login();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SportsPlay</title>
</head>
<body>
  <h1>Welcome to SportsPlay!</h1>
  <p>This is the parent dashboard. You can view your teams, enroll in new ones, and manage your profile.</p>
  <a href="auth/logout.php">Logout</a>
</body>
</html>

