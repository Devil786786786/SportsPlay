<?php
require 'config.php';

// Only allow logged-in admins
if (empty($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header('Location: login.php');
    exit;
}

$currentAdminId = (int)$_SESSION['user_id'];
$errors = [];
$success = '';

// =============== ACTIONS ===============

// Promote (from dropdown)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['promote_user_id'])) {
    $id = (int)$_POST['promote_user_id'];
    if ($id <= 0) {
        $errors[] = 'Please select a user to promote.';
    } elseif ($id === $currentAdminId) {
        $errors[] = 'You cannot change your own role here.';
    } else {
        // Only promote non-admin, non-coach
        $stmt = $pdo->prepare(
            'UPDATE users
             SET is_coach = 1
             WHERE user_id = :id AND is_admin = 0 AND is_coach = 0'
        );
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() > 0) {
            $success = 'User promoted to coach.';
        } else {
            $errors[] = 'Could not promote this user (maybe already a coach or admin).';
        }
    }
}

// Demote coach
if (isset($_GET['demote'])) {
    $id = (int)$_GET['demote'];
    if ($id > 0 && $id !== $currentAdminId) {
        $stmt = $pdo->prepare(
            'UPDATE users
             SET is_coach = 0
             WHERE user_id = :id'
        );
        $stmt->execute(['id' => $id]);
    }
    header('Location: admin_coaches.php');
    exit;
}

// Delete coach
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0 && $id !== $currentAdminId) {
        // Only delete non-admins
        $stmt = $pdo->prepare(
            'DELETE FROM users
             WHERE user_id = :id AND is_admin = 0'
        );
        $stmt->execute(['id' => $id]);
    }
    header('Location: admin_coaches.php');
    exit;
}

// =============== LOAD DATA ===============

// Users that can be promoted to coach (only normal users)
$promotableStmt = $pdo->query(
    'SELECT user_id, first_name, last_name, email
     FROM users
     WHERE is_admin = 0 AND is_coach = 0
     ORDER BY first_name, last_name, email'
);
$promotableUsers = $promotableStmt->fetchAll();

// Current coaches list
$coachesStmt = $pdo->query(
    'SELECT user_id, first_name, last_name, email, created_at
     FROM users
     WHERE is_coach = 1
     ORDER BY first_name, last_name, email'
);
$coaches = $coachesStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sportsplay - Manage Coaches</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<div class="admin-wrapper">
    <aside class="sidebar">
        <div class="sidebar-logo"><span>Sportsplay</span></div>
        <div class="sidebar-title">Navigation</div>
        <ul class="nav-list">
            <li class="nav-item"><a href="dashboard.php" class="nav-link">Dashboard</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Teams</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Players</a></li>
            <li class="nav-item"><a href="admin_coaches.php" class="nav-link active">Coaches</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Parents</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Matches</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Trainings</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Leagues</a></li>
        </ul>
        <div class="sidebar-footer">
            &copy; <?php echo date('Y'); ?> Sportsplay
        </div>
    </aside>

    <main class="main">
        <header class="topbar">
            <div class="admin-info">
                <div class="admin-name-role">
                    <div class="name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
                    <div class="role">Admin</div>
                </div>
                <div class="admin-avatar">
                    <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                </div>
                <form method="post" action="logout.php">
                    <button type="submit" class="btn-logout">Logout</button>
                </form>
            </div>
        </header>

        <section class="content">
            <h2 class="section-title">Manage Coaches</h2>
            <p class="section-subtitle">
                Promote existing users to coaches and manage current coaches.
            </p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $e): ?>
                        <div><?php echo htmlspecialchars($e); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <!-- PROMOTE SECTION -->
            <div class="table-card" style="margin-bottom: 20px;">
                <div class="table-header">
                    <div class="table-header-title">Promote User to Coach</div>
                </div>

                <form method="post" action="admin_coaches.php">
                    <div class="form-row">
                        <div class="form-control" style="max-width: 320px;">
                            <label for="promote_user_id">Select User</label>
                            <select id="promote_user_id" name="promote_user_id">
                                <option value="">-- choose user --</option>
                                <?php foreach ($promotableUsers as $u): ?>
                                    <?php
                                        $full = trim($u['first_name'] . ' ' . ($u['last_name'] ?? ''));
                                        $label = $full !== '' ? $full . ' (' . $u['email'] . ')' : $u['email'];
                                    ?>
                                    <option value="<?php echo (int)$u['user_id']; ?>">
                                        <?php echo htmlspecialchars($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn-add">Make Coach</button>
                </form>

                <?php if (empty($promotableUsers)): ?>
                    <p style="font-size:13px; color:#666; margin-top:10px;">
                        There are no regular users available to promote.
                    </p>
                <?php endif; ?>
            </div>

            <!-- CURRENT COACHES -->
            <div class="table-card">
                <div class="table-header">
                    <div class="table-header-title">Current Coaches</div>
                </div>

                <table class="coaches-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Since</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($coaches)): ?>
                        <tr><td colspan="5">No coaches yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($coaches as $index => $c): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td>
                                    <?php
                                        $full = trim($c['first_name'] . ' ' . ($c['last_name'] ?? ''));
                                        echo htmlspecialchars($full ?: '(no name)');
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($c['email']); ?></td>
                                <td><?php echo htmlspecialchars($c['created_at']); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a class="btn-small edit"
                                           href="admin_coaches.php?demote=<?php echo (int)$c['user_id']; ?>"
                                           onclick="return confirm('Remove coach status for this user?');">
                                            Remove Coach
                                        </a>
                                        <a class="btn-small delete"
                                           href="admin_coaches.php?delete=<?php echo (int)$c['user_id']; ?>"
                                           onclick="return confirm('Delete this coach (user account) completely?');">
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
        </section>
    </main>
</div>
</body>
</html>
