<?php
require_once __DIR__ . '/../config/config.php';

require_role('admin');

$pageTitle = 'Dashboard';
$activeNav = 'dashboard';

// Lightweight metrics (works with current DB.sql which uses roles + user_roles)
$totalUsers  = (int)($pdo->query('SELECT COUNT(*) FROM users')->fetchColumn() ?? 0);
$activeUsers = (int)($pdo->query('SELECT COUNT(*) FROM users WHERE is_active = 1')->fetchColumn() ?? 0);
$admins      = (int)($pdo->query("SELECT COUNT(DISTINCT ur.user_id) FROM user_roles ur JOIN roles r ON r.role_id = ur.role_id WHERE r.role_name = 'admin'")->fetchColumn() ?? 0);
$coaches     = (int)($pdo->query("SELECT COUNT(DISTINCT ur.user_id) FROM user_roles ur JOIN roles r ON r.role_id = ur.role_id WHERE r.role_name = 'coach'")->fetchColumn() ?? 0);
$new7d       = (int)($pdo->query('SELECT COUNT(*) FROM users WHERE created_at >= (NOW() - INTERVAL 7 DAY)')->fetchColumn() ?? 0);
$googleUsers = (int)($pdo->query("
    SELECT COUNT(DISTINCT user_id)
    FROM oauth_identities
    WHERE provider = 'google'
")->fetchColumn() ?? 0);


// Last / this month registrations
$newThisMonth = (int)($pdo->query(
  "SELECT COUNT(*) FROM users WHERE created_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01')"
)->fetchColumn() ?? 0);

$newLastMonth = (int)($pdo->query(
  "SELECT COUNT(*) FROM users WHERE created_at >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01') AND created_at < DATE_FORMAT(CURDATE(), '%Y-%m-01')"
)->fetchColumn() ?? 0);

// Until the DB schema is expanded, approximate players as non-admin, non-coach accounts. (updated nto mach new DB)
$approxPlayers = (int)($pdo->query("SELECT COUNT(*) FROM players")->fetchColumn() ?? 0);

// Recent registrations
$recentUsers = $pdo->query("
  SELECT
    u.first_name,
    u.last_name,
    u.email,
    u.created_at,
    COALESCE(oi.provider, 'local') AS provider
  FROM users u
  LEFT JOIN (
    SELECT user_id, MIN(provider) AS provider
    FROM oauth_identities
    GROUP BY user_id
  ) oi ON oi.user_id = u.user_id
  ORDER BY u.created_at DESC
  LIMIT 8
")->fetchAll();

// Monthly registrations (last 12 months)
$rows = $pdo->query(
  "SELECT DATE_FORMAT(created_at, '%Y-%m') ym, DATE_FORMAT(created_at, '%b') mon, COUNT(*) cnt
   FROM users
   WHERE created_at >= (DATE_SUB(CURDATE(), INTERVAL 11 MONTH))
   GROUP BY ym, mon
   ORDER BY ym ASC"
)->fetchAll();

$months = [];
// Build 12-month skeleton so missing months show as 0
$start = new DateTime('first day of this month');
$start->modify('-11 months');
for ($i=0; $i<12; $i++) {
  $ym = $start->format('Y-m');
  $mon = $start->format('M');
  $months[$ym] = ['label' => strtoupper($mon), 'cnt' => 0];
  $start->modify('+1 month');
}
foreach ($rows as $r) {
  if (isset($months[$r['ym']])) $months[$r['ym']]['cnt'] = (int)$r['cnt'];
}
$maxCnt = 0;
foreach ($months as $m) $maxCnt = max($maxCnt, (int)$m['cnt']);
$maxCnt = max($maxCnt, 1);

$userFirst = trim(explode(' ', (string)($_SESSION['user_name'] ?? 'Admin'))[0] ?? 'Admin');

include __DIR__ . '/../includes/admin_header.php';
?>

<div class="sp-dashboard-hero">
  <section class="sp-card" style="padding:18px 18px 10px;">
    <div class="sp-hero">
      <div>
        <h1>Welcome Back, <?php echo htmlspecialchars($userFirst); ?>!</h1>
        <p>Admin Dashboard</p>
      </div>

      <div class="sp-toolbar">
        <div class="sp-field">
          <label for="season">Season</label>
          <select id="season" class="sp-select">
            <option>2026 Spring</option>
            <option>2025 Fall</option>
            <option>2025 Summer</option>
          </select>
        </div>

        <div class="sp-field">
          <label for="sport">Sport</label>
          <select id="sport" class="sp-select">
            <option>All sports</option>
            <option>Soccer</option>
            <option>Basketball</option>
            <option>Baseball</option>
          </select>
        </div>

        <button class="sp-btn sp-btn--ghost" type="button" data-dialog-open="#dlgQuickCreate">
          <i class="fa-solid fa-plus"></i>&nbsp; Quick Create
        </button>
      </div>
    </div>

    <div style="margin-top:18px" class="sp-kpis">
      <div class="sp-kpi">
        <div class="label">Active Users</div>
        <div class="value"><?php echo number_format($activeUsers); ?></div>
        <div class="meta">of <?php echo number_format($totalUsers); ?> total accounts</div>
      </div>

      <div class="sp-kpi">
        <div class="label">New Registrations (Last Month)</div>
        <div class="value"><?php echo number_format($newLastMonth); ?></div>
        <div class="meta"><?php echo number_format($newThisMonth); ?> so far this month</div>
      </div>

      <div class="sp-kpi">
        <div class="label">Players (Approx.)</div>
        <div class="value"><?php echo number_format($approxPlayers); ?></div>
        <div class="meta">non-admin, non-coach users</div>
      </div>

      <div class="sp-kpi">
        <div class="label">Staff Accounts</div>
        <div class="value"><?php echo number_format($coaches + $admins); ?></div>
        <div class="meta"><?php echo number_format($coaches); ?> coaches • <?php echo number_format($admins); ?> admins</div>
      </div>
    </div>
  </section>

  <section class="sp-card sp-surface">
    <div class="sp-card__hd">
      <div>
        <div class="sp-card__title">Players Activity</div>
        <div class="sp-card__sub">Registrations per month</div>
      </div>
      <div>
        <select class="sp-select" style="height:34px; border:none; background:rgba(255,255,255,.14); color:#fff;">
          <option>Month</option>
          <option>Week</option>
          <option>Day</option>
        </select>
      </div>
    </div>

    <div class="sp-card__bd">
      <div class="sp-bars" aria-label="Monthly registrations">
        <?php foreach ($months as $m):
          $h = (int)round(($m['cnt'] / $maxCnt) * 100);
          $h = max($h, 6); // keep visible
        ?>
          <div class="bar" title="<?php echo htmlspecialchars($m['label']); ?>: <?php echo (int)$m['cnt']; ?>">
            <span style="height: <?php echo $h; ?>%"></span>
            <i><?php echo htmlspecialchars($m['label']); ?></i>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</div>

<section class="sp-split">
  <div class="sp-card sp-surface">
    <div class="sp-card__hd">
      <div>
        <div class="sp-card__title">New Registrations</div>
        <div class="sp-card__sub">Target tracking (demo)</div>
      </div>
      <span class="sp-pill" style="background:rgba(255,255,255,.14); border-color:rgba(255,255,255,.20); color:#fff;">
        <i class="fa-solid fa-bullseye"></i> Goals
      </span>
    </div>

    <div class="sp-card__bd">
      <div class="sp-progress">
        <div class="sp-progress-row">
          <div>Week</div>
          <div class="sp-track"><span style="width: 34%"></span></div>
          <div style="text-align:right">34%</div>
        </div>
        <div class="sp-progress-row">
          <div>Month</div>
          <div class="sp-track"><span style="width: 62%"></span></div>
          <div style="text-align:right">62%</div>
        </div>
        <div class="sp-progress-row">
          <div>Year</div>
          <div class="sp-track"><span style="width: 78%"></span></div>
          <div style="text-align:right">78%</div>
        </div>
      </div>

      <div style="margin-top:14px; display:flex; gap:10px; flex-wrap:wrap;">
        <span class="sp-pill sp-pill--success"><i class="fa-solid fa-circle-check"></i> Paid: 81%</span>
        <span class="sp-pill sp-pill--warning"><i class="fa-solid fa-circle-exclamation"></i> Unpaid: 14%</span>
        <span class="sp-pill sp-pill--danger"><i class="fa-solid fa-ban"></i> Cancelled: 5%</span>
      </div>
    </div>
  </div>

  <div class="sp-card">
    <div class="sp-card__hd">
      <div>
        <div class="sp-card__title">Quick Actions</div>
        <div class="sp-card__sub">Most common admin tasks</div>
      </div>
    </div>
    <div class="sp-card__bd">
      <div class="sp-grid" style="grid-template-columns: repeat(2, 1fr); gap:12px;">
        <button class="sp-btn sp-btn--ghost" type="button" data-dialog-open="#dlgCreateLeague"><i class="fa-solid fa-layer-group"></i>&nbsp; Create League</button>
        <button class="sp-btn sp-btn--ghost" type="button" data-dialog-open="#dlgCreateTeam"><i class="fa-solid fa-people-group"></i>&nbsp; Create Team</button>
        <a class="sp-btn sp-btn--ghost" href="admin_coaches.php" style="text-decoration:none; display:flex; align-items:center; justify-content:center;">
          <i class="fa-solid fa-user-tie"></i>&nbsp; Manage Coaches
        </a>
        <a class="sp-btn sp-btn--ghost" href="admin_reports.php" style="text-decoration:none; display:flex; align-items:center; justify-content:center;">
          <i class="fa-solid fa-chart-line"></i>&nbsp; View Reports
        </a>
      </div>

      <div class="sp-alert" style="margin-top:14px;">
        <strong>Tip:</strong> keep everything configurable by <em>Season → Sport → League</em>. This aligns with SportsPlay's MVP goal to support multiple sports and high configurability.
      </div>
    </div>
  </div>
</section>

<section class="sp-split" style="margin-top:16px">
  <div class="sp-card sp-surface sp-table-card">
    <div class="sp-card__hd">
      <div>
        <div class="sp-card__title">Latest Registrations</div>
        <div class="sp-card__sub">Most recent accounts (from current DB)</div>
      </div>
      <span class="sp-pill" style="background:rgba(255,255,255,.14); border-color:rgba(255,255,255,.20); color:#fff;">
        <i class="fa-solid fa-user-plus"></i> Live
      </span>
    </div>

    <div class="sp-card__bd">
      <div class="sp-filterbar" style="margin-bottom:10px;">
        <div class="sp-filterbar__left">
          <div class="sp-search" style="width:100%">
            <i class="fa-solid fa-magnifying-glass icon"></i>
            <input data-table-search="#tblRecent" type="text" placeholder="Search name, email, provider…" style="width:100%; max-width:none; background:rgba(255,255,255,.12); border-color:rgba(255,255,255,.20); color:#fff;" />
          </div>
        </div>
        <div class="sp-filterbar__right">
          <select class="sp-select" data-table-filter="#tblRecent" data-col="2" style="height:38px; background:rgba(255,255,255,.12); border-color:rgba(255,255,255,.20); color:#fff;">
            <option value="">Any provider</option>
            <option>local</option>
            <option>google</option>
          </select>
        </div>
      </div>

      <div class="sp-table-wrap">
        <table id="tblRecent" class="sp-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th style="width:120px">Provider</th>
              <th style="width:160px">Created</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentUsers as $u):
              $name = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
              if ($name === '') $name = '—';
              $prov = $u['provider'] ?: 'local';
            ?>
              <tr>
                <td><strong><?php echo htmlspecialchars($name); ?></strong></td>
                <td><?php echo htmlspecialchars((string)$u['email']); ?></td>
                <td><span class="sp-pill" style="background:rgba(255,255,255,.14); border-color:rgba(255,255,255,.18); color:#fff;"><?php echo htmlspecialchars($prov); ?></span></td>
                <td><?php echo htmlspecialchars((string)$u['created_at']); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="sp-card sp-surface sp-table-card">
    <div class="sp-card__hd">
      <div>
        <div class="sp-card__title">Search Players</div>
        <div class="sp-card__sub">Fast lookup (demo data)</div>
      </div>
      <span class="sp-pill" style="background:rgba(255,255,255,.14); border-color:rgba(255,255,255,.20); color:#fff;">
        <i class="fa-solid fa-magnifying-glass"></i> Quick
      </span>
    </div>

    <div class="sp-card__bd">
      <div class="sp-filterbar" style="margin-bottom:10px;">
        <div class="sp-filterbar__left">
          <div class="sp-search" style="width:100%">
            <i class="fa-solid fa-magnifying-glass icon"></i>
            <input data-table-search="#tblPlayers" type="text" placeholder="Search by name, team, league…" style="width:100%; max-width:none; background:rgba(255,255,255,.12); border-color:rgba(255,255,255,.20); color:#fff;" />
          </div>
        </div>

        <div class="sp-filterbar__right">
          <select class="sp-select" data-table-filter="#tblPlayers" data-col="3" style="height:38px; background:rgba(255,255,255,.12); border-color:rgba(255,255,255,.20); color:#fff;">
            <option value="">All leagues</option>
            <option>U14</option>
            <option>U16</option>
            <option>U18</option>
          </select>
        </div>
      </div>

      <div class="sp-table-wrap">
        <table id="tblPlayers" class="sp-table">
          <thead>
            <tr>
              <th style="width:56px">ID</th>
              <th>Name</th>
              <th>Team</th>
              <th>League</th>
              <th style="width:120px">Payment</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>11</td><td>John Smith</td><td>FC Miami</td><td>U14</td><td><span class="sp-pill" style="background:rgba(23,178,106,.18); border-color:rgba(23,178,106,.30); color:#fff;">Paid</span></td></tr>
            <tr><td>12</td><td>Marco Polo</td><td>FC New York</td><td>U16</td><td><span class="sp-pill" style="background:rgba(247,144,9,.18); border-color:rgba(247,144,9,.30); color:#fff;">Unpaid</span></td></tr>
            <tr><td>13</td><td>Ana Ruiz</td><td>LA United</td><td>U18</td><td><span class="sp-pill" style="background:rgba(23,178,106,.18); border-color:rgba(23,178,106,.30); color:#fff;">Paid</span></td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<!-- ===== Dialogs (UI only; wire to backend later) ===== -->

<dialog id="dlgQuickCreate" class="sp-dialog">
  <div class="sp-dialog__hd">
    <div class="sp-dialog__title">Quick Create</div>
    <div class="sp-card__sub">Shortcuts to create core entities (UI only)</div>
  </div>
  <div class="sp-dialog__bd">
    <div class="sp-grid" style="grid-template-columns: repeat(2, 1fr); gap:12px;">
      <button class="sp-btn sp-btn--ghost" type="button" data-dialog-open="#dlgCreateLeague"><i class="fa-solid fa-layer-group"></i>&nbsp; League</button>
      <button class="sp-btn sp-btn--ghost" type="button" data-dialog-open="#dlgCreateTeam"><i class="fa-solid fa-people-group"></i>&nbsp; Team</button>
      <button class="sp-btn sp-btn--ghost" type="button" data-dialog-open="#dlgCreateMatch"><i class="fa-solid fa-trophy"></i>&nbsp; Match</button>
      <button class="sp-btn sp-btn--ghost" type="button" data-dialog-open="#dlgCreateTraining"><i class="fa-solid fa-dumbbell"></i>&nbsp; Training</button>
    </div>

    <div class="sp-form-actions">
      <button class="sp-btn sp-btn--ghost" type="button" data-dialog-close>Close</button>
    </div>
  </div>
</dialog>

<dialog id="dlgCreateLeague" class="sp-dialog">
  <div class="sp-dialog__hd">
    <div class="sp-dialog__title">Create League</div>
    <div class="sp-card__sub">Season → Sport → League structure</div>
  </div>
  <div class="sp-dialog__bd">
    <form>
      <div class="sp-form-grid">
        <div class="sp-col-6">
          <label class="sp-card__sub">Season</label>
          <select class="sp-select" style="width:100%"><option>2026 Spring</option><option>2025 Fall</option></select>
        </div>
        <div class="sp-col-6">
          <label class="sp-card__sub">Sport</label>
          <select class="sp-select" style="width:100%"><option>Soccer</option><option>Basketball</option></select>
        </div>
        <div class="sp-col-6">
          <label class="sp-card__sub">League name</label>
          <input class="sp-input" style="width:100%" placeholder="e.g., U14" />
        </div>
        <div class="sp-col-6">
          <label class="sp-card__sub">Age range</label>
          <input class="sp-input" style="width:100%" placeholder="e.g., 13–14" />
        </div>
        <div class="sp-col-12">
          <label class="sp-card__sub">Notes</label>
          <input class="sp-input" style="width:100%" placeholder="Rules, roster size, etc." />
        </div>
      </div>

      <div class="sp-form-actions">
        <button class="sp-btn sp-btn--ghost" type="button" data-dialog-close>Cancel</button>
        <button class="sp-btn sp-btn--pill" type="button">Create (UI)</button>
      </div>
    </form>
  </div>
</dialog>

<dialog id="dlgCreateTeam" class="sp-dialog">
  <div class="sp-dialog__hd">
    <div class="sp-dialog__title">Create Team</div>
    <div class="sp-card__sub">Assign coach, league, and roster limits</div>
  </div>
  <div class="sp-dialog__bd">
    <form>
      <div class="sp-form-grid">
        <div class="sp-col-6">
          <label class="sp-card__sub">Team name</label>
          <input class="sp-input" style="width:100%" placeholder="e.g., FC Miami Blue" />
        </div>
        <div class="sp-col-6">
          <label class="sp-card__sub">League</label>
          <select class="sp-select" style="width:100%"><option>U14</option><option>U16</option><option>U18</option></select>
        </div>
        <div class="sp-col-6">
          <label class="sp-card__sub">Coach</label>
          <select class="sp-select" style="width:100%"><option>Unassigned</option><option>Coach #1</option><option>Coach #2</option></select>
        </div>
        <div class="sp-col-6">
          <label class="sp-card__sub">Roster cap</label>
          <input class="sp-input" style="width:100%" placeholder="e.g., 18" />
        </div>
      </div>

      <div class="sp-form-actions">
        <button class="sp-btn sp-btn--ghost" type="button" data-dialog-close>Cancel</button>
        <button class="sp-btn sp-btn--pill" type="button">Create (UI)</button>
      </div>
    </form>
  </div>
</dialog>

<dialog id="dlgCreateMatch" class="sp-dialog">
  <div class="sp-dialog__hd">
    <div class="sp-dialog__title">Create Match</div>
    <div class="sp-card__sub">Schedule games + allow results entry (authorized)</div>
  </div>
  <div class="sp-dialog__bd">
    <form>
      <div class="sp-form-grid">
        <div class="sp-col-6"><label class="sp-card__sub">Home team</label><input class="sp-input" style="width:100%" placeholder="Search team…" /></div>
        <div class="sp-col-6"><label class="sp-card__sub">Away team</label><input class="sp-input" style="width:100%" placeholder="Search team…" /></div>
        <div class="sp-col-6"><label class="sp-card__sub">Date</label><input class="sp-input" style="width:100%" placeholder="YYYY-MM-DD" /></div>
        <div class="sp-col-6"><label class="sp-card__sub">Time</label><input class="sp-input" style="width:100%" placeholder="HH:MM" /></div>
        <div class="sp-col-12"><label class="sp-card__sub">Location</label><input class="sp-input" style="width:100%" placeholder="Field name, address" /></div>
      </div>
      <div class="sp-form-actions">
        <button class="sp-btn sp-btn--ghost" type="button" data-dialog-close>Cancel</button>
        <button class="sp-btn sp-btn--pill" type="button">Create (UI)</button>
      </div>
    </form>
  </div>
</dialog>

<dialog id="dlgCreateTraining" class="sp-dialog">
  <div class="sp-dialog__hd">
    <div class="sp-dialog__title">Create Training</div>
    <div class="sp-card__sub">Practice sessions + attendance</div>
  </div>
  <div class="sp-dialog__bd">
    <form>
      <div class="sp-form-grid">
        <div class="sp-col-6"><label class="sp-card__sub">Team</label><input class="sp-input" style="width:100%" placeholder="Search team…" /></div>
        <div class="sp-col-6"><label class="sp-card__sub">Coach</label><input class="sp-input" style="width:100%" placeholder="Coach name…" /></div>
        <div class="sp-col-6"><label class="sp-card__sub">Date</label><input class="sp-input" style="width:100%" placeholder="YYYY-MM-DD" /></div>
        <div class="sp-col-6"><label class="sp-card__sub">Time</label><input class="sp-input" style="width:100%" placeholder="HH:MM" /></div>
        <div class="sp-col-12"><label class="sp-card__sub">Notes</label><input class="sp-input" style="width:100%" placeholder="Bring shin guards, etc." /></div>
      </div>
      <div class="sp-form-actions">
        <button class="sp-btn sp-btn--ghost" type="button" data-dialog-close>Cancel</button>
        <button class="sp-btn sp-btn--pill" type="button">Create (UI)</button>
      </div>
    </form>
  </div>
</dialog>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
