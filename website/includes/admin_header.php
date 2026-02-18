<?php
// Admin header partial
// Expected variables: $pageTitle (string), $activeNav (string)
$pageTitle = $pageTitle ?? 'Admin';
$activeNav = $activeNav ?? '';

$userName = $_SESSION['user_name'] ?? 'Admin';
$initial = strtoupper(substr(trim($userName), 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo htmlspecialchars($pageTitle); ?> · SportsPlay</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="../assets/css/admin-ui.css" />
</head>
<body>
  <div class="sp-admin">
    <?php include __DIR__ . '/admin_sidebar.php'; ?>

    <div class="sp-main">
      <header class="sp-topbar">
        <div class="sp-topbar__left">
          <button class="sp-burger" type="button" data-burger aria-label="Open menu">
            <i class="fa-solid fa-bars"></i>
          </button>

          <div class="sp-breadcrumb">
            <div>
              <div class="sp-breadcrumb__title"><?php echo htmlspecialchars($pageTitle); ?></div>
              <div class="sp-role">Admin Console</div>
            </div>
          </div>
        </div>

        <div class="sp-topbar__center">
          <div class="sp-role-title" aria-label="Current role">
            <span class="label">Role</span>
            <span class="value">Admin</span>
          </div>
        </div>

        <div class="sp-topbar__right">
          <div class="sp-search" aria-label="Quick search">
            <i class="fa-solid fa-magnifying-glass icon"></i>
            <input type="text" placeholder="Search teams, players, coaches…" />
          </div>

          <div class="sp-avatar" title="<?php echo htmlspecialchars($userName); ?>">
            <?php echo htmlspecialchars($initial); ?>
          </div>

          <form method="post" action="<?php echo htmlspecialchars(app_url('auth/logout.php')); ?>" style="margin:0;">
            <button class="sp-btn sp-btn--pill" type="submit">Logout</button>
          </form>
        </div>
      </header>

      <main class="sp-content">
