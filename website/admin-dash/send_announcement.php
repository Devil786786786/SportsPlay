<?php
include __DIR__ . '/../includes/admin_guard.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: admin_parents.php');
  exit;
}

$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');
$userIds = $_POST['user_ids'] ?? [];

if ($subject === '' || $message === '') {
  $_SESSION['flash_error'] = 'Subject and message are required.';
  header('Location: admin_parents.php');
  exit;
}

if (!is_array($userIds) || count($userIds) === 0) {
  $_SESSION['flash_error'] = 'Select at least one parent.';
  header('Location: admin_parents.php');
  exit;
}

// sanitize ids
$userIds = array_values(array_unique(array_filter(array_map('intval', $userIds), fn($v) => $v > 0)));
if (!$userIds) {
  $_SESSION['flash_error'] = 'Invalid selection.';
  header('Location: admin_parents.php');
  exit;
}

// Fetch recipient emails: active, non-admin/non-coach
$placeholders = implode(',', array_fill(0, count($userIds), '?'));
$sql = "
  SELECT u.email
  FROM users u
  WHERE u.user_id IN ($placeholders)
    AND u.is_active = 1
    AND NOT EXISTS (
      SELECT 1
      FROM user_roles ur
      JOIN roles r ON r.role_id = ur.role_id
      WHERE ur.user_id = u.user_id AND r.role_name IN ('admin','coach')
    )
";
$stmt = $pdo->prepare($sql);
$stmt->execute($userIds);
$emails = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (!$emails) {
  $_SESSION['flash_error'] = 'No valid active parent emails found.';
  header('Location: admin_parents.php');
  exit;
}

// Logged-in sender (Reply-To)
$senderId = (int)($_SESSION['user_id'] ?? 0);
$sender = null;
if ($senderId > 0) {
  $st = $pdo->prepare("SELECT email, first_name, last_name FROM users WHERE user_id = :id LIMIT 1");
  $st->execute(['id' => $senderId]);
  $sender = $st->fetch();
}
$replyEmail = $sender['email'] ?? null;
$replyName  = trim(($sender['first_name'] ?? '') . ' ' . ($sender['last_name'] ?? ''));
if ($replyName === '') $replyName = $replyEmail ?: 'SportsPlay Admin';

// SMTP config
$mailCfg = require __DIR__ . '/../config/mail.php';

try {
  $mail = new PHPMailer(true);
  $mail->CharSet = 'UTF-8';
  $mail->isSMTP();
  $mail->Host       = $mailCfg['smtp_host'];
  $mail->SMTPAuth   = true;
  $mail->Username   = $mailCfg['smtp_user'];
  $mail->Password   = $mailCfg['smtp_pass'];
  $mail->SMTPSecure = $mailCfg['smtp_secure'];
  $mail->Port       = (int)$mailCfg['smtp_port'];

  // Sent by app mailbox
  $mail->setFrom($mailCfg['from_email'], $mailCfg['from_name']);

  // Replies go to the logged-in user
  if ($replyEmail) {
    $mail->addReplyTo($replyEmail, $replyName);
  }

  // Privacy: one To, recipients in BCC
  $mail->addAddress($mailCfg['from_email'], $mailCfg['from_name']);
  foreach ($emails as $e) $mail->addBCC($e);

  $mail->isHTML(false);
  $mail->Subject = $subject;

  // Optional: include sender in body
  $prefix = $replyEmail ? "Sent by: {$replyEmail}\n\n" : "";
  $mail->Body = $prefix . $message;

  $mail->send();
  $_SESSION['flash_success'] = 'Announcement sent to ' . count($emails) . ' parent(s).';
} catch (Throwable $e) {
  $_SESSION['flash_error'] = 'Email failed. Check Gmail app password / SMTP settings.';
}

header('Location: admin_parents.php');
exit;
