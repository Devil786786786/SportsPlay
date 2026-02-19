<?php
include __DIR__ . '/../includes/admin_guard.php';

$pageTitle = 'Players';
$activeNav = 'players';

$errors = [];
$success = '';

function calc_age(?string $dob): string {
  if (!$dob) return '—';
  try {
    $d = new DateTime($dob);
    $now = new DateTime();
    return (string)$now->diff($d)->y;
  } catch (Throwable $e) {
    return '—';
  }
}

// ---------- ADD PLAYER ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_player'])) {
  $first  = trim($_POST['first_name'] ?? '');
  $last   = trim($_POST['last_name'] ?? '');
  $dob    = trim($_POST['date_of_birth'] ?? '');
  $gender = trim($_POST['gender'] ?? '');
  $pos    = trim($_POST['position'] ?? '');
  $jersey = trim($_POST['jersey_no'] ?? '');
  $parent_id = (int)($_POST['parent_id'] ?? 0);

  if ($first === '' || $last === '' || $dob === '') {
    $errors[] = 'First name, last name, and date of birth are required.';
  } else {
    try {
      $stmt = $pdo->prepare("
        INSERT INTO players (first_name, last_name, date_of_birth, gender, position, jersey_no, parent_id)
        VALUES (:first, :last, :dob, :gender, :pos, :jersey, :pid)
      ");
      $stmt->execute([
        'first'  => $first,
        'last'   => $last,
        'dob'    => $dob,
        'gender' => $gender !== '' ? $gender : null,
        'pos'    => $pos !== '' ? $pos : null,
        'jersey' => $jersey !== '' ? (int)$jersey : null,
        'pid'    => $parent_id > 0 ? $parent_id : null,
      ]);
      $success = 'Player added.';
    } catch (PDOException $e) {
      $errors[] = 'Could not add player.';
    }
  }
}

// ---------- DELETE PLAYER ----------
if (isset($_GET['delete'])) {
  $pid = (int)($_GET['delete'] ?? 0);
  if ($pid > 0) {
    try {
      $pdo->prepare("DELETE FROM players WHERE player_id = :pid")->execute(['pid' => $pid]);
    } catch (PDOException $e) {
      $errors[] = 'Could not delete player.';
    }
  }
  header('Location: admin_players.php');
  exit;
}

// ---------- LOAD DATA ----------
$totalPlayers = (int)($pdo->query("SELECT COUNT(*) FROM players")->fetchColumn() ?: 0);

// Parent dropdown options (real parents = rows in parents join users)
$parentOptions = $pdo->query("
  SELECT p.parent_id, u.email, u.first_name, u.last_name
  FROM parents p
  JOIN users u ON u.user_id = p.user_id
  ORDER BY u.first_name, u.last_name, u.email
")->fetchAll();

$players = $pdo->query("
  SELECT
    pl.player_id,
    pl.first_name,
    pl.last_name,
    pl.date_of_birth,
    pl.gender,
    pl.position,
    pl.jersey_no,
    u.email AS parent_email
  FROM players pl
  LEFT JOIN parents p ON p.parent_id = pl.parent_id
  LEFT JOIN users u ON u.user_id = p.user_id
  ORDER BY pl.last_name, pl.first_name
")->fetchAll();

include __DIR__ . '/../includes/admin_header.php';
?>

<section class="sp-card">
  <div class="sp-card__hd">
    <div>
      <div class="sp-card__title">Players</div>
      <div class="sp-card__sub">Player profiles (connected to DB). Team/league/payment can come next.</div>
    </div>

    <div class="sp-actions">
      <button class="sp-btn sp-btn--ghost" type="button" data-dialog-open="#dlgPlayerCreate">
        <i class="fa-solid fa-plus"></i>&nbsp; Add Player
      </button>
      <a class="sp-btn sp-btn--pill" href="admin_players.php" style="text-decoration:none;">
        <i class="fa-solid fa-arrows-rotate"></i>&nbsp; Refresh
      </a>
    </div>
  </div>

  <div class="sp-card__bd">

    <div class="sp-kpis" style="margin-bottom:14px;">
      <div class="sp-kpi"><div class="label">Total Players</div><div class="value"><?php echo number_format($totalPlayers); ?></div><div class="meta">from DB</div></div>
      <div class="sp-kpi"><div class="label">Paid Registrations</div><div class="value">—</div><div class="meta">payments not built yet</div></div>
      <div class="sp-kpi"><div class="label">Unpaid</div><div class="value">—</div><div class="meta">payments not built yet</div></div>
      <div class="sp-kpi"><div class="label">Forms Missing</div><div class="value">—</div><div class="meta">forms not built yet</div></div>
    </div>

    <?php if (!empty($errors)): ?>
      <div class="sp-alert sp-alert--error" style="margin-bottom:12px;">
        <?php foreach ($errors as $e): ?><div><?php echo htmlspecialchars($e); ?></div><?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="sp-alert sp-alert--success" style="margin-bottom:12px;">
        <?php echo htmlspecialchars($success); ?>
      </div>
    <?php endif; ?>

    <div class="sp-filterbar">
      <div class="sp-filterbar__left">
        <div class="sp-search">
          <i class="fa-solid fa-magnifying-glass icon"></i>
          <input data-table-search="#tblPlayersAdmin" type="text" placeholder="Search player, parent email…" />
        </div>
      </div>
    </div>

    <div style="height:12px"></div>

    <div class="sp-table-wrap" style="max-height: 540px; border:1px solid var(--line)">
      <table id="tblPlayersAdmin" class="sp-table sp-table--light">
        <thead>
          <tr>
            <th style="width:44px"><input type="checkbox" aria-label="Select all" /></th>
            <th>Player</th>
            <th style="width:92px">Age</th>
            <th>Team</th>
            <th style="width:90px">League</th>
            <th>Parent</th>
            <th style="width:120px">Payment</th>
            <th style="width:140px">Forms</th>
            <th style="width:180px">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($players)): ?>
            <tr><td colspan="9" class="sp-card__sub">No players yet. Click “Add Player”.</td></tr>
          <?php else: ?>
            <?php foreach ($players as $pl): ?>
              <?php
                $name = trim(($pl['first_name'] ?? '') . ' ' . ($pl['last_name'] ?? ''));
                $age = calc_age($pl['date_of_birth'] ?? null);
                $parentEmail = $pl['parent_email'] ?? 'Unlinked';
              ?>
              <tr>
                <td><input type="checkbox" /></td>
                <td>
                  <strong><?php echo htmlspecialchars($name); ?></strong>
                  <div class="sp-card__sub">
                    DOB: <?php echo htmlspecialchars($pl['date_of_birth']); ?>
                    <?php if (!empty($pl['position'])): ?> • Pos: <?php echo htmlspecialchars($pl['position']); ?><?php endif; ?>
                    <?php if (!empty($pl['jersey_no'])): ?> • #<?php echo (int)$pl['jersey_no']; ?><?php endif; ?>
                  </div>
                </td>
                <td><?php echo htmlspecialchars($age); ?></td>
                <td>—</td>
                <td>—</td>
                <td><?php echo htmlspecialchars($parentEmail); ?></td>
                <td>—</td>
                <td>—</td>
                <td>
                  <div class="sp-actions">
                    <button class="sp-btn-tag primary" type="button">View</button>
                    <a class="sp-btn-tag danger"
                       href="admin_players.php?delete=<?php echo (int)$pl['player_id']; ?>"
                       onclick="return confirm('Delete this player?');">Delete</a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</section>

<dialog id="dlgPlayerCreate" class="sp-dialog">
  <div class="sp-dialog__hd">
    <div class="sp-dialog__title">Add Player</div>
    <div class="sp-card__sub">Creates a real DB record in <code>players</code>.</div>
  </div>
  <div class="sp-dialog__bd">
    <form method="post" action="admin_players.php">
      <input type="hidden" name="add_player" value="1" />

      <div class="sp-form-grid">
        <div class="sp-col-6">
          <label class="sp-card__sub">First name</label>
          <input class="sp-input" style="width:100%" name="first_name" required />
        </div>
        <div class="sp-col-6">
          <label class="sp-card__sub">Last name</label>
          <input class="sp-input" style="width:100%" name="last_name" required />
        </div>
        <div class="sp-col-6">
          <label class="sp-card__sub">Date of birth</label>
          <input class="sp-input" style="width:100%" name="date_of_birth" placeholder="YYYY-MM-DD" required />
        </div>
        <div class="sp-col-6">
          <label class="sp-card__sub">Gender (optional)</label>
          <input class="sp-input" style="width:100%" name="gender" placeholder="M / F / ..." />
        </div>
        <div class="sp-col-6">
          <label class="sp-card__sub">Position (optional)</label>
          <input class="sp-input" style="width:100%" name="position" placeholder="Forward" />
        </div>
        <div class="sp-col-6">
          <label class="sp-card__sub">Jersey # (optional)</label>
          <input class="sp-input" style="width:100%" name="jersey_no" placeholder="10" />
        </div>

        <div class="sp-col-12">
          <label class="sp-card__sub">Parent (optional)</label>
          <select class="sp-select" style="width:100%" name="parent_id">
            <option value="">-- Unlinked --</option>
            <?php foreach ($parentOptions as $po): ?>
              <?php
                $label = trim(($po['first_name'] ?? '') . ' ' . ($po['last_name'] ?? ''));
                if ($label === '') $label = $po['email'];
                else $label .= ' (' . $po['email'] . ')';
              ?>
              <option value="<?php echo (int)$po['parent_id']; ?>"><?php echo htmlspecialchars($label); ?></option>
            <?php endforeach; ?>
          </select>

          <?php if (empty($parentOptions)): ?>
            <div class="sp-card__sub" style="margin-top:6px;">
              No parent profiles exist yet. Go to <a href="admin_parents.php">Parents</a> and click “Create profile”.
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="sp-form-actions">
        <button class="sp-btn sp-btn--ghost" type="button" data-dialog-close>Cancel</button>
        <button class="sp-btn sp-btn--pill" type="submit">Add Player</button>
      </div>
    </form>
  </div>
</dialog>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
