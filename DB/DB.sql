-- Adminer 5.4.1 MariaDB 10.4.32-MariaDB dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `provider` varchar(20) NOT NULL DEFAULT 'local',
  `google_sub` varchar(64) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `avatar_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `is_coach` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `google_sub` (`google_sub`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`user_id`, `email`, `provider`, `google_sub`, `password_hash`, `first_name`, `last_name`, `phone`, `avatar_url`, `created_at`, `ip_address`, `metadata`, `is_active`, `is_admin`, `is_coach`) VALUES
(1,	'djole786@gmail.com',	'local',	NULL,	'$2y$10$XuPOgTgAAAorjYS.1.NXTe2tIYgFmVNIK.JXEwL96Zdp6iZSyXShu',	'Devil',	'',	'0953628294',	NULL,	'2025-12-05 00:04:47',	'::1',	NULL,	1,	1,	0),
(2,	'da@gmail.com',	'local',	NULL,	'$2y$10$pdW7Vg7t7THHlB4aKrdKFuU1f0GD6RHYt4t9nQNyfnFMX91McK7BO',	'MojaBaba',	'',	'0995555555',	NULL,	'2025-12-05 00:49:17',	'::1',	NULL,	1,	0,	1),
(4,	'djordje.vulevic123@gmail.com',	'google',	'117282614343571782213',	'$2y$10$wDQLwwGtBNIMfQ2s8EZ4reg/x8Z5rLkvcH6LJvOBLOS9TeFB4btoy',	'djordje',	'vulevic',	NULL,	'https://lh3.googleusercontent.com/a/ACg8ocJch0nMsRt_1Vii2YiM120EsBoKuMCYxarbtoZ3BWPI-9zx3JEB=s96-c',	'2026-02-02 03:10:45',	'::1',	NULL,	1,	0,	1),
(5,	'devilw530@gmail.com',	'google',	'111284404197079025542',	'$2y$10$iWmiGiT1y9w3Te2hhEv2Sud3niQPpmjwCejZvflHBcpDTcj1Dzumq',	'Devil',	'Warrior',	NULL,	'https://lh3.googleusercontent.com/a/ACg8ocLDL5j_8myoc4s55V0eixxm8X2962XwEbI6kmX2G_5UvHwKFA=s96-c',	'2026-02-02 19:55:54',	'::1',	NULL,	1,	0,	0);

-- 2026-02-02 20:00:04 UTC