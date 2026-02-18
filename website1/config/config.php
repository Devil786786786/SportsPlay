<?php
// config.php

// Database settings for XAMPP
$db_host = 'localhost';       // localhost
$db_name = 'sportsplay';      // the DB you created in Adminer
$db_user = 'root';
$db_pass = 'sportsplay123';   // the password we set for root

// Google Sign-In (Google Identity Services)
$google_client_id = '1020968959954-jrqm4lre2si4iv0bgt3tb5qp75fjrk1c.apps.googleusercontent.com';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Default port (3306):
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";

    // If you use a custom port like 3307, use this instead:
    // $dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4";

    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    exit('Database connection failed: ' . $e->getMessage());
}

session_start();
