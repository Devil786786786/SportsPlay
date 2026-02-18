<?php
require_once __DIR__ . '/../config/config.php';

// Only allow logged-in admins
require_role('admin');

$pageTitle = 'Coaches';
$activeNav = 'coaches';

$currentAdminId = (int)($_SESSION['user_id'] ?? 0);
$errors = [];
$success = '';

// Role IDs
$adminRoleId = (int)($pdo->query("SELECT role_id FROM roles WHERE role_name = 'admin' LIMIT 1")->fetchColumn() ?: 0);
$coachRoleId = (int)($pdo->query("SELECT role_id FROM roles WHERE role_name = 'coach' LIMIT 1")->fetchColumn() ?: 0);

// =============== ACTIONS ===============

// Promote (from dropdown): assign coach role + ensure coaches profile row exists
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['promote_user_id'])) {
    $id = (int)($_POST['promote_user_id'] ?? 0);

    if ($id <= 0) {
        $errors[] = 'Please select a user to promote.';
    } elseif ($id === $currentAdminId) {
        $errors[] = 'You cannot change your own role here.';
    } elseif ($coachRoleId === 0) {
        $errors[] = "Role 'coach' was not found in the database.";
    } else {
        // Block promoting admins
        $chkAdmin = $pdo->prepare('SELECT COUNT(*) FROM user_roles WHERE user_id = :uid AND role_id = :rid');
        $chkAdmin->execute(['uid' => $id, 'rid' => $adminRoleId]);
        if ((int)$chkAdmin->fetchColumn() > 0) {
            $errors[] = 'You cannot promote an admin to coach.';
        } else {
            // Add coach role if missing
            $chkCoach = $pdo->prepare('SELECT COUNT(*) FROM user_roles WHERE user_id = :uid AND role_id = :rid');
            $chkCoach->execute(['uid' => $id, 'rid' => $coachRoleId]);

            if ((int)$chkCoach->fetchColumn() === 0) {
                $ins = $pdo->prepare('INSERT INTO user_roles (user_id, role_id) VALUES (:uid, :rid)');
                $ins->execute(['uid' => $id, 'rid' => $coachRoleId]);
            }

            // Ensure coaches table row exists
            $chkProfile = $pdo->prepare('SELECT COUNT(*) FROM coaches WHERE user_id = :uid');
            $chkProfile->execute(['uid' => $id]);
            if ((int)$chkProfile->fetchColumn() === 0) {
                $insCoach = $pdo->prepare('INSERT INTO coaches (user_id) VALUES (:uid)');
                $insCoach->execute(['uid' => $id]);
            }

            $success = 'User promoted to coach.';
        }
    }
}

// Demote coach: remove coach role + remove coaches profile row
if (isset($_GET['demote'])) {
    $id = (int)$_GET['demote'];
    if ($id > 0 && $id !== $currentAdminId) {
        if ($coachRoleId > 0) {
            $del = $pdo->prepare('DELETE FROM user_roles WHERE user_id = :uid AND role_id = :rid');
            $del->execute(['uid' => $id, 'rid' => $coachRoleId]);
        }
        $delCoach = $pdo->prepare('DELETE FROM coaches WHERE user_id = :uid');
        $delCoach->execute(['uid' => $id]);
    }
    header('Location: admin_coaches.php');
    exit;
}

// Delete coach/user: delete user record (blocked for admins)
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0 && $id !== $currentAdminId) {
        // Only delete non-admins
        $chkAdmin = $pdo->prepare('SELECT COUNT(*) FROM user_roles WHERE user_id = :uid AND role_id = :rid');
        $chkAdmin->execute(['uid' => $id, 'rid' => $adminRoleId]);
        if ((int)$chkAdmin->fetchColumn() === 0) {
            $stmt = $pdo->prepare('DELETE FROM users WHERE user_id = :id');
            $stmt->execute(['id' => $id]);
        }
    }
    header('Location: admin_coaches.php');
    exit;
}

// =============== LOAD DATA ===============

// Users who are not admin and not coach (promotable)
$promotableUsers = $pdo->query(
    "SELECT u.user_id, u.first_name, u.last_name, u.email
     FROM users u
     WHERE u.is_active = 1
       AND NOT EXISTS (
         SELECT 1 FROM user_roles ur
         JOIN roles r ON r.role_id = ur.role_id
         WHERE ur.user_id = u.user_id AND r.role_name IN ('admin','coach')
       )
     ORDER BY u.first_name, u.last_name, u.email"
)->fetchAll();

// Current coaches (users with coach role)
$coaches = $pdo->query(
    "SELECT u.user_id, u.first_name, u.last_name, u.email, u.created_at
     FROM users u
     JOIN user_roles ur ON ur.user_id = u.user_id
     JOIN roles r ON r.role_id = ur.role_id
     WHERE r.role_name = 'coach'
     ORDER BY u.first_name, u.last_name, u.email"
)->fetchAll();

include __DIR__ . '/../includes/admin_header.php';

?>

<section class="sp-card">
  <div class="sp-card__hd">
    <div>
      <div class="sp-card__title">Manage Coaches</div>
      <div class="sp-card__sub">Promote existing users to coaches and manage current coaches.</div>
    </div>

    <div class="sp-actions">
      <a class="sp-btn sp-btn--ghost" href="admin_teams.php" style="text-decoration:none;"><i class="fa-solid fa-people-group"></i>&nbsp; Manage Teams</a>
      <a class="sp-btn sp-btn--pill" href="admin_schedule.php" style="text-decoration:none;"><i class="fa-solid fa-calendar-days"></i>&nbsp; View Schedule</a>
    </div>
  </div>

  <div class="sp-card__bd">

    <div class="sp-kpis" style="margin-bottom:14px;">
      <div class="sp-kpi">
        <div class="label">Total Coaches</div>
        <div class="value"><?php echo number_format(count($coaches)); ?></div>
        <div class="meta">accounts with coach role</div>
      </div>
      <div class="sp-kpi">
        <div class="label">Promotable Users</div>
        <div class="value"><?php echo number_format(count($promotableUsers)); ?></div>
        <div class="meta">regular accounts available</div>
      </div>
      <div class="sp-kpi">
        <div class="label">Policy</div>
        <div class="value">1 → 1</div>
        <div class="meta">coach ↔ team (recommended)</div>
      </div>
      <div class="sp-kpi">
        <div class="label">Quick Actions</div>
        <div class="value">Roles</div>
        <div class="meta">promote / remove / delete</div>
      </div>
    </div>

    <?php if (!empty($errors)): ?>
      <div class="sp-alert sp-alert--error" style="margin-bottom:12px;">
        <?php foreach ($errors as $e): ?>
          <div><?php echo htmlspecialchars($e); ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="sp-alert sp-alert--success" style="margin-bottom:12px;">
        <?php echo htmlspecialchars($success); ?>
      </div>
    <?php endif; ?>

    <div class="sp-split">
      <div class="sp-card" style="box-shadow:none; border:1px solid var(--line);">
        <div class="sp-card__hd">
          <div>
            <div class="sp-card__title">Promote User to Coach</div>
            <div class="sp-card__sub">Only regular users are listed.</div>
          </div>
        </div>

        <div class="sp-card__bd">
          <form method="post" action="admin_coaches.php">
            <div class="sp-form-grid">
              <div class="sp-col-12">
                <label class="sp-card__sub" for="promote_user_id">Select user</label>
                <select id="promote_user_id" name="promote_user_id" class="sp-select" style="width:100%">
                  <option value="">-- choose user --</option>
                  <?php foreach ($promotableUsers as $u): ?>
                    <?php
                      $full = trim($u['first_name'] . ' ' . ($u['last_name'] ?? ''));
                      $label = $full !== '' ? $full . ' (' . $u['email'] . ')' : $u['email'];
                    ?>
                    <option value="<?php echo (int)$u['user_id']; ?>"><?php echo htmlspecialchars($label); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="sp-col-12">
                <div class="sp-form-actions" style="justify-content:flex-start">
                  <button type="submit" class="sp-btn sp-btn--pill"><i class="fa-solid fa-user-plus"></i>&nbsp; Make Coach</button>
                </div>
              </div>
            </div>
          </form>

          <?php if (empty($promotableUsers)): ?>
            <div class="sp-alert" style="margin-top:10px;">
              There are no regular users available to promote.
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="sp-card" style="box-shadow:none; border:1px solid var(--line);">
        <div class="sp-card__hd">
          <div>
            <div class="sp-card__title">Current Coaches</div>
            <div class="sp-card__sub">Search, demote, or delete coach accounts.</div>
          </div>
        </div>

        <div class="sp-card__bd">
          <div class="sp-filterbar" style="margin-bottom:10px;">
            <div class="sp-filterbar__left">
              <div class="sp-search" style="width:100%">
                <i class="fa-solid fa-magnifying-glass icon"></i>
                <input data-table-search="#tblCoaches" type="text" placeholder="Search coaches…" style="width:100%; max-width:none" />
              </div>
            </div>
          </div>

          <div class="sp-table-wrap" style="max-height: 420px; border:1px solid var(--line)">
            <table id="tblCoaches" class="sp-table sp-table--light">
              <thead>
                <tr>
                  <th style="width:56px">#</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th style="width:160px">Since</th>
                  <th style="width:220px">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($coaches)): ?>
                  <tr><td colspan="5">No coaches yet.</td></tr>
                <?php else: ?>
                  <?php foreach ($coaches as $index => $c): ?>
                    <?php $full = trim($c['first_name'] . ' ' . ($c['last_name'] ?? '')); ?>
                    <tr>
                      <td><?php echo $index + 1; ?></td>
                      <td><strong><?php echo htmlspecialchars($full ?: '(no name)'); ?></strong></td>
                      <td><?php echo htmlspecialchars($c['email']); ?></td>
                      <td><?php echo htmlspecialchars($c['created_at']); ?></td>
                      <td>
                        <div class="sp-actions">
                          <a class="sp-btn-tag" href="admin_coaches.php?demote=<?php echo (int)$c['user_id']; ?>" onclick="return confirm('Remove coach status for this user?');">Remove Coach</a>
                          <a class="sp-btn-tag danger" href="admin_coaches.php?delete=<?php echo (int)$c['user_id']; ?>" onclick="return confirm('Delete this coach (user account) completely?');">Delete</a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <div class="sp-alert" style="margin-top:12px">
            <strong>Admin autonomy idea:</strong> add coach assignment rules (one coach ↔ one team) and prevent double-assignment at the database level.
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
                      