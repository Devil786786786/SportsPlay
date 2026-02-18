<?php
require_once __DIR__ . '/../config/config.php';

// Admin-only guard (new roles schema)
require_role('admin');
