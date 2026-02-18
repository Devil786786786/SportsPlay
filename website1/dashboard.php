<?php
require_once __DIR__ . '/config/config.php';
if (empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if (!empty($_SESSION['is_admin'])) {
  header('Location: admin_dashboard.php');
  exit;
}

if (!empty($_SESSION['is_coach'])) {
  header('Location: coach_dashboard.php');
  exit;
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
</body>
</html>

