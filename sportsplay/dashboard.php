<?php
require 'config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sportsplay - Dashboard</title>
</head>
<body>
    <h1>Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?> 👋</h1>
    <p>You’re logged in to Sportsplay.</p>
    <p><a href="logout.php">Log out</a></p>
</body>
</html>
