<?php
include __DIR__ . '/../includes/admin_guard.php';

$pageTitle = 'Parents';
$activeNav = 'parents';

$currentAdminId = (int)($_SESSION['user_id'] ?? 0);
$errors = [];
$success = '';

// Role IDs (parent role might exist or not; code handles both)
$adminRoleId  = (int)($pdo->query("SELECT role_id FROM roles WHERE role_name='admin'  LIMIT 1")->fetchColumn() ?: 0);
$coachRoleId  = (int)($pdo->query("SELECT role_id FROM roles WHERE role_name='coach'  LIMIT 1")->fetchColumn() ?: 0);
$parentRoleId = (int)($pdo->query("SELECT role_id FROM roles WHERE role_name='parent' LIMIT 1")->fetchColumn() ?: 0);

// Prevent editing admins/coaches from this page
function is_privileged_user(PDO $pdo, int $uid): bool
{
  $stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM user_roles ur
    JOIN roles r ON r.role_id = ur.role_id
    WHERE ur.user_id = :uid AND r.role_name IN ('admin','coach')
  ");
  $stmt->execute(['uid' => $uid]);
  return ((int)$stmt->fetchColumn()) > 0;
}

// ---- ACTIONS ----

// Create missing parent profile row (parents.user_id) if not exists
if (isset($_GET['make_profile'])) {
  $uid = (int)($_GET['make_profile'] ?? 0);
  if ($uid > 0 && $uid !== $currentAdminId && !is_privileged_user($pdo, $uid)) {
    $chk = $pdo->prepare("SELECT parent_id FROM parents WHERE user_id = :uid LIMIT 1");
    $chk->execute(['uid' => $uid]);
    $pid = (int)($chk->fetchColumn() ?: 0);

    if ($pid === 0) {
      try {
        $pdo->prepare("INSERT INTO parents (user_id) VALUES (:uid)")->execute(['uid' => $uid]);
        $success = 'Parent profile created.';
      } catch (PDOException $e) {
        $errors[] = 'Could not create parent profile.';
      }
    } else {
      $success = 'Parent profile already exists.';
    }
  }
  header('Location: admin_parents.php');
  exit;
}

// Toggle enable/disable for parent users (is_active)
if (isset($_GET['toggle'])) {
  $uid = (int)($_GET['toggle'] ?? 0);
  if ($uid > 0 && $uid !== $currentAdminId && !is_privileged_user($pdo, $uid)) {
    try {
      $pdo->prepare("UPDATE users SET is_active = IF(is_active=1,0,1) WHERE user_id = :uid")
        ->execute(['uid' => $uid]);
    } catch (PDOException $e) {
      $errors[] = 'Could not update status.';
    }
  }
  header('Location: admin_parents.php');
  exit;
}

// Delete parent user (also deletes their parent profile; optionally delete their players too)
if (isset($_GET['delete'])) {
  $uid = (int)($_GET['delete'] ?? 0);
  if ($uid > 0 && $uid !== $currentAdminId && !is_privileged_user($pdo, $uid)) {
    try {
      // Find parent_id and delete players first (so you don’t keep orphan players)
      $pidStmt = $pdo->prepare("SELECT parent_id FROM parents WHERE user_id = :uid LIMIT 1");
      $pidStmt->execute(['uid' => $uid]);
      $parent_id = (int)($pidStmt->fetchColumn() ?: 0);

      if ($parent_id > 0) {
        $pdo->prepare("DELETE FROM players WHERE parent_id = :pid")->execute(['pid' => $parent_id]);
      }

      // Delete user (cascades user_roles, parents, oauth_identities if FK exists)
      $pdo->prepare("DELETE FROM users WHERE user_id = :uid")->execute(['uid' => $uid]);
    } catch (PDOException $e) {
      $errors[] = 'Could not delete user.';
    }
  }
  header('Location: admin_parents.php');
  exit;
}

// ---- MAKE USER A PARENT (assign role + create parents row) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['make_parent'])) {
  $uid = (int)($_POST['user_id'] ?? 0);

  if ($uid <= 0 || $uid === $currentAdminId) {
    $_SESSION['flash_error'] = 'Invalid user selected.';
    header('Location: admin_parents.php');
    exit;
  }

  if (is_privileged_user($pdo, $uid)) {
    $_SESSION['flash_error'] = 'That user is an admin/coach. Remove that role first.';
    header('Location: admin_parents.php');
    exit;
  }

  try {
    $pdo->beginTransaction();

    // ensure parent role exists
    $parentRoleId = (int)($pdo->query("SELECT role_id FROM roles WHERE role_name='parent' LIMIT 1")->fetchColumn() ?: 0);
    if ($parentRoleId === 0) {
      $pdo->exec("INSERT IGNORE INTO roles (role_name) VALUES ('parent')");
      $parentRoleId = (int)($pdo->query("SELECT role_id FROM roles WHERE role_name='parent' LIMIT 1")->fetchColumn() ?: 0);
    }
    if ($parentRoleId === 0) throw new Exception('Parent role missing');

    // add parent role (unique key prevents duplicates)
    $pdo->prepare("INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (:uid, :rid)")
      ->execute(['uid' => $uid, 'rid' => $parentRoleId]);

    // create parents profile row if missing
    $chk = $pdo->prepare("SELECT parent_id FROM parents WHERE user_id = :uid LIMIT 1");
    $chk->execute(['uid' => $uid]);
    $pid = (int)($chk->fetchColumn() ?: 0);

    if ($pid === 0) {
      $pdo->prepare("INSERT INTO parents (user_id) VALUES (:uid)")
        ->execute(['uid' => $uid]);
    }

    $pdo->commit();
    $_SESSION['flash_success'] = 'User is now a parent.';
  } catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    $_SESSION['flash_error'] = 'Could not make user a parent.';
  }

  header('Location: admin_parents.php');
  exit;
}

// ---- LOAD DATA ----
// Provider label from oauth_identities (google/local)
$providerJoin = "
  LEFT JOIN (
    SELECT user_id, MIN(provider) AS provider
    FROM oauth_identities
    GROUP BY user_id
  ) oi ON oi.user_id = u.user_id
";

if ($parentRoleId > 0) {
  // If you have a real 'parent' role
  $sql = "
    SELECT
      u.user_id, u.first_name, u.last_name, u.email, u.phone, u.is_active,
      p.parent_id,
      COALESCE(oi.provider, 'local') AS provider,
      COUNT(pl.player_id) AS players_count
    FROM users u
    JOIN user_roles urp ON urp.user_id = u.user_id AND urp.role_id = :parentRoleId
    LEFT JOIN parents p ON p.user_id = u.user_id
    LEFT JOIN players pl ON pl.parent_id = p.parent_id
    $providerJoin
    GROUP BY u.user_id, u.first_name, u.last_name, u.email, u.phone, u.is_active, p.parent_id, oi.provider
    ORDER BY u.created_at DESC
  ";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['parentRoleId' => $parentRoleId]);
  $parents = $stmt->fetchAll();
} else {
  // Otherwise treat “parents” as everyone who is not admin/coach
  $parents = $pdo->query("
    SELECT
      u.user_id, u.first_name, u.last_name, u.email, u.phone, u.is_active,
      p.parent_id,
      COALESCE(oi.provider, 'local') AS provider,
      COUNT(pl.player_id) AS players_count
    FROM users u
    LEFT JOIN parents p ON p.user_id = u.user_id
    LEFT JOIN players pl ON pl.parent_id = p.parent_id
    $providerJoin
    WHERE NOT EXISTS (
      SELECT 1
      FROM user_roles ur
      JOIN roles r ON r.role_id = ur.role_id
      WHERE ur.user_id = u.user_id AND r.role_name IN ('admin','coach')
    )
    GROUP BY u.user_id, u.first_name, u.last_name, u.email, u.phone, u.is_active, p.parent_id, oi.provider
    ORDER BY u.created_at DESC
  ")->fetchAll();
}

// Users you can convert to parent (non-admin/coach, not already parent-role)
if ($parentRoleId > 0) {
  $stmt = $pdo->prepare("
    SELECT u.user_id, u.first_name, u.last_name, u.email
    FROM users u
    WHERE u.user_id <> :me
      AND NOT EXISTS (
        SELECT 1
        FROM user_roles ur
        JOIN roles r ON r.role_id = ur.role_id
        WHERE ur.user_id = u.user_id AND r.role_name IN ('admin','coach')
      )
      AND NOT EXISTS (
        SELECT 1
        FROM user_roles urp
        WHERE urp.user_id = u.user_id AND urp.role_id = :parentRoleId
      )
    ORDER BY u.created_at DESC
  ");
  $stmt->execute(['me' => $currentAdminId, 'parentRoleId' => $parentRoleId]);
  $eligibleUsers = $stmt->fetchAll();
} else {
  // if there's no parent role in DB, show users missing a parent profile row
  $stmt = $pdo->prepare("
    SELECT u.user_id, u.first_name, u.last_name, u.email
    FROM users u
    LEFT JOIN parents p ON p.user_id = u.user_id
    WHERE u.user_id <> :me
      AND p.parent_id IS NULL
      AND NOT EXISTS (
        SELECT 1
        FROM user_roles ur
        JOIN roles r ON r.role_id = ur.role_id
        WHERE ur.user_id = u.user_id AND r.role_name IN ('admin','coach')
      )
    ORDER BY u.created_at DESC
  ");
  $stmt->execute(['me' => $currentAdminId]);
  $eligibleUsers = $stmt->fetchAll();
}

include __DIR__ . '/../includes/admin_header.php';
?>

<section class="sp-card">
  <div class="sp-card__hd">
    <div>
      <div class="sp-card__title">Parents</div>
      <div class="sp-card__sub">Manage parent accounts, contact info, payments, and messaging.</div>
    </div>

    <div class="sp-actions">
      <button id="btnAnnouncement" class="sp-btn sp-btn--ghost" type="button">
        <i class="fa-solid fa-paper-plane"></i>&nbsp; Send Announcement
      </button>

      <button id="btnMakeParent" class="sp-btn sp-btn--ghost" type="button">
        <i class="fa-solid fa-user-plus"></i>&nbsp; Add Parent
      </button>

      <a class="sp-btn sp-btn--pill" href="admin_parents.php" style="text-decoration:none;">
        <i class="fa-solid fa-arrows-rotate"></i>&nbsp; Refresh
      </a>
    </div>
  </div>

  <div class="sp-card__bd">

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

    <?php if (!empty($_SESSION['flash_error'])): ?>
      <div class="sp-alert sp-alert--error" style="margin-bottom:12px;">
        <?php echo htmlspecialchars($_SESSION['flash_error']);
        unset($_SESSION['flash_error']); ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_success'])): ?>
      <div class="sp-alert sp-alert--success" style="margin-bottom:12px;">
        <?php echo htmlspecialchars($_SESSION['flash_success']);
        unset($_SESSION['flash_success']); ?>
      </div>
    <?php endif; ?>


    <div class="sp-filterbar">
      <div class="sp-filterbar__left">
        <div class="sp-search">
          <i class="fa-solid fa-magnifying-glass icon"></i>
          <input data-table-search="#tblParents" type="text" placeholder="Search parents by name, email, phone…" />
        </div>

        <select class="sp-select" data-table-filter="#tblParents" data-col="5">
          <option value="">Any balance</option>
          <option>Overdue</option>
          <option>Paid</option>
        </select>

        <select class="sp-select" data-table-filter="#tblParents" data-col="6">
          <option value="">Any status</option>
          <option>Active</option>
          <option>Disabled</option>
        </select>
      </div>

      <div class="sp-filterbar__right">
        <button class="sp-btn sp-btn--ghost" type="button"><i class="fa-solid fa-file-export"></i>&nbsp; Export</button>
      </div>
    </div>

    <div style="height:12px"></div>

    <div class="sp-table-wrap" style="max-height: 540px; border:1px solid var(--line)">
      <table id="tblParents" class="sp-table sp-table--light">
        <thead>
          <tr>
            <th style="width:44px"><input type="checkbox" id="chkAllParents"></th>
            <th>Parent</th>
            <th>Email</th>
            <th style="width:160px">Phone</th>
            <th style="width:90px">Players</th>
            <th style="width:120px">Balance</th>
            <th style="width:120px">Status</th>
            <th style="width:220px">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($parents)): ?>
            <tr>
              <td colspan="8" class="sp-card__sub">No parent accounts found.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($parents as $p): ?>
              <?php
              $full = trim(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? ''));
              if ($full === '') $full = '(no name)';
              $status = ((int)$p['is_active'] === 1) ? 'Active' : 'Disabled';
              $prov = $p['provider'] ?? 'local';
              ?>
              <tr>
                <td><input type="checkbox" class="js-parent" value="<?php echo (int)$p['user_id']; ?>"></td>
                <td>
                  <strong><?php echo htmlspecialchars($full); ?></strong>
                  <div class="sp-card__sub">Provider: <?php echo htmlspecialchars($prov); ?></div>
                  <?php if (empty($p['parent_id'])): ?>
                    <div class="sp-card__sub">No parent profile row yet</div>
                  <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($p['email'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($p['phone'] ?? ''); ?></td>
                <td><?php echo (int)$p['players_count']; ?></td>
                <td>—</td>
                <td>
                  <?php if ($status === 'Active'): ?>
                    <span class="sp-pill sp-pill--success">Active</span>
                  <?php else: ?>
                    <span class="sp-pill">Disabled</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="sp-actions">
                    <a class="sp-btn-tag primary" href="mailto:<?php echo htmlspecialchars($p['email'] ?? ''); ?>">Message</a>

                    <?php if (empty($p['parent_id'])): ?>
                      <a class="sp-btn-tag" href="admin_parents.php?make_profile=<?php echo (int)$p['user_id']; ?>">Create profile</a>
                    <?php else: ?>
                      <a class="sp-btn-tag" href="admin_players.php">Players</a>
                    <?php endif; ?>

                    <a class="sp-btn-tag danger" href="admin_parents.php?toggle=<?php echo (int)$p['user_id']; ?>">
                      <?php echo ($status === 'Active') ? 'Disable' : 'Enable'; ?>
                    </a>

                    <a class="sp-btn-tag danger"
                      href="admin_parents.php?delete=<?php echo (int)$p['user_id']; ?>"
                      onclick="return confirm('Delete this parent account? This will also delete their players.');">
                      Delete
                    </a>
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

<dialog id="dlgAnnouncement" class="sp-dialog">
  <div class="sp-dialog__hd">
    <div class="sp-dialog__title">Send announcement</div>
    <div class="sp-card__sub">Emails selected parents (BCC). Reply goes to you.</div>
  </div>

  <div class="sp-dialog__bd">
    <form method="post" action="send_announcement.php">
      <div class="sp-form-grid">
        <div class="sp-col-12">
          <label class="sp-card__sub">Subject</label>
          <input class="sp-input" style="width:100%" name="subject" required>
        </div>

        <div class="sp-col-12">
          <label class="sp-card__sub">Message</label>
          <textarea class="sp-input" style="width:100%; min-height:140px;" name="message" required></textarea>
        </div>
      </div>

      <div id="recipientInputs"></div>

      <div class="sp-form-actions">
        <button class="sp-btn sp-btn--ghost" type="button" data-dialog-close>Cancel</button>
        <button class="sp-btn sp-btn--pill" type="submit">Send</button>
      </div>
    </form>
  </div>
</dialog>

<script>
  (function() {
    const dlg = document.getElementById('dlgAnnouncement');
    const btn = document.getElementById('btnAnnouncement');
    const all = document.getElementById('chkAllParents');
    const holder = document.getElementById('recipientInputs');

    if (all) {
      all.addEventListener('change', () => {
        document.querySelectorAll('.js-parent').forEach(cb => cb.checked = all.checked);
      });
    }

    function openAnnouncement() {
      const selected = Array.from(document.querySelectorAll('.js-parent:checked')).map(x => x.value);
      if (selected.length === 0) {
        alert('Select at least one parent first.');
        return;
      }
      holder.innerHTML = '';
      selected.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'user_ids[]';
        input.value = id;
        holder.appendChild(input);
      });
      dlg.showModal();
    }

    if (btn) btn.addEventListener('click', openAnnouncement);
  })();
</script>

<dialog id="dlgMakeParent" class="sp-dialog">
  <div class="sp-dialog__hd">
    <div class="sp-dialog__title">Make user a parent</div>
    <div class="sp-card__sub">Assigns the parent role + creates the parent profile row.</div>
  </div>

  <div class="sp-dialog__bd">
    <form method="post" action="admin_parents.php">
      <input type="hidden" name="make_parent" value="1">

      <div class="sp-form-grid">
        <div class="sp-col-12">
          <label class="sp-card__sub">Select user</label>
          <select class="sp-select" style="width:100%" name="user_id" required>
            <option value="">-- Choose a user --</option>
            <?php foreach ($eligibleUsers as $u): ?>
              <?php
                $label = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
                if ($label === '') $label = $u['email'];
                else $label .= ' (' . $u['email'] . ')';
              ?>
              <option value="<?php echo (int)$u['user_id']; ?>"><?php echo htmlspecialchars($label); ?></option>
            <?php endforeach; ?>
          </select>

          <?php if (empty($eligibleUsers)): ?>
            <div class="sp-card__sub" style="margin-top:6px;">No eligible users found.</div>
          <?php endif; ?>
        </div>
      </div>

      <div class="sp-form-actions">
        <button class="sp-btn sp-btn--ghost" type="button" onclick="document.getElementById('dlgMakeParent').close()">Cancel</button>
        <button class="sp-btn sp-btn--pill" type="submit">Make Parent</button>
      </div>
    </form>
  </div>
</dialog>

<script>
(function(){
  const btn = document.getElementById('btnMakeParent');
  const dlg = document.getElementById('dlgMakeParent');
  if (btn && dlg) btn.addEventListener('click', () => dlg.showModal());
})();
</script>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>