<?php
$activeNav = $activeNav ?? '';

$navPrimary = [
  ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'fa-gauge-high', 'href' => 'admin_dashboard.php'],
  ['key' => 'teams', 'label' => 'Teams', 'icon' => 'fa-people-group', 'href' => 'admin_teams.php'],
  ['key' => 'players', 'label' => 'Players', 'icon' => 'fa-person-running', 'href' => 'admin_players.php'],
  ['key' => 'coaches', 'label' => 'Coaches', 'icon' => 'fa-user-tie', 'href' => 'admin_coaches.php'],
  ['key' => 'parents', 'label' => 'Parents', 'icon' => 'fa-people-roof', 'href' => 'admin_parents.php'],
  ['key' => 'matches', 'label' => 'Matches', 'icon' => 'fa-trophy', 'href' => 'admin_matches.php'],
  ['key' => 'trainings', 'label' => 'Trainings', 'icon' => 'fa-dumbbell', 'href' => 'admin_trainings.php'],
  ['key' => 'leagues', 'label' => 'Leagues', 'icon' => 'fa-layer-group', 'href' => 'admin_leagues.php'],
  ['key' => 'schedule', 'label' => 'Schedule', 'icon' => 'fa-calendar-days', 'href' => 'admin_schedule.php'],
];

$navSecondary = [
  ['key' => 'content', 'label' => 'News & Content', 'icon' => 'fa-newspaper', 'href' => 'admin_content.php'],
  ['key' => 'reports', 'label' => 'Reports', 'icon' => 'fa-chart-line', 'href' => 'admin_reports.php'],
  ['key' => 'settings', 'label' => 'Settings', 'icon' => 'fa-gear', 'href' => 'admin_settings.php'],
];
?>

<aside class="sp-sidebar" aria-label="Admin navigation">
  <div class="sp-brand">
    <div class="sp-brand__mark">
      <div class="sp-brand__logo">sportsplay</div>
      <div class="sp-brand__sub">Admin dashboard</div>
    </div>

    <a href="admin_dashboard.php" class="sp-btn sp-btn--ghost" style="text-decoration:none; color:inherit;" title="Go to dashboard">
      <i class="fa-solid fa-arrow-right"></i>
    </a>
  </div>

  <div class="sp-sidebar__section-title">Navigation</div>
  <ul class="sp-nav">
    <?php foreach ($navPrimary as $item): ?>
      <li>
        <a href="<?php echo htmlspecialchars($item['href']); ?>" class="<?php echo ($activeNav === $item['key']) ? 'active' : ''; ?>">
          <span class="ico"><i class="fa-solid <?php echo htmlspecialchars($item['icon']); ?>"></i></span>
          <span><?php echo htmlspecialchars($item['label']); ?></span>
        </a>
      </li>
    <?php endforeach; ?>

    <li style="height:12px"></li>
    <li class="sp-sidebar__section-title" style="list-style:none; margin:4px 8px 10px;">Admin</li>

    <?php foreach ($navSecondary as $item): ?>
      <li>
        <a href="<?php echo htmlspecialchars($item['href']); ?>" class="<?php echo ($activeNav === $item['key']) ? 'active' : ''; ?>">
          <span class="ico"><i class="fa-solid <?php echo htmlspecialchars($item['icon']); ?>"></i></span>
          <span><?php echo htmlspecialchars($item['label']); ?></span>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>

  <div class="sp-sidebar__footer">
    <div style="opacity:.9; font-weight:600;">SportsPlay Teams, Inc.</div>
    <div style="margin-top:6px;">&copy; <?php echo date('Y'); ?> · v0.1</div>
  </div>
</aside>
