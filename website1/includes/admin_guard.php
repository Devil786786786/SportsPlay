<?php
require_once __DIR__ . '/../config/config.php';

if (empty($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
  header('Location: auth/login.php');
  exit;
}
