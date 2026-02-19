-- Adminer 5.4.1 MariaDB 10.4.32-MariaDB dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `account_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `can_manage_schedules` tinyint(1) NOT NULL DEFAULT 0,
  `can_post_results` tinyint(1) NOT NULL DEFAULT 0,
  `can_edit_team_pages` tinyint(1) NOT NULL DEFAULT 0,
  `can_manage_league_pages` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`account_id`),
  KEY `idx_accounts_user_id` (`user_id`),
  CONSTRAINT `fk_accounts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `coaches`;
CREATE TABLE `coaches` (
  `coach_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `bio` text DEFAULT NULL,
  PRIMARY KEY (`coach_id`),
  KEY `idx_coaches_user_id` (`user_id`),
  CONSTRAINT `fk_coaches_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `oauth_identities`;
CREATE TABLE `oauth_identities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `provider` varchar(30) NOT NULL,
  `provider_sub` varchar(128) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_provider_sub` (`provider`,`provider_sub`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `fk_oauth_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `parents`;
CREATE TABLE `parents` (
  `parent_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`parent_id`),
  KEY `idx_parents_user_id` (`user_id`),
  CONSTRAINT `fk_parents_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `players`;
CREATE TABLE `players` (
  `player_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `jersey_no` int(10) unsigned DEFAULT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`player_id`),
  KEY `idx_players_parent_id` (`parent_id`),
  CONSTRAINT `fk_players_parent` FOREIGN KEY (`parent_id`) REFERENCES `parents` (`parent_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `role_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `sports`;
CREATE TABLE `sports` (
  `sport_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`sport_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`user_id`, `email`, `password_hash`, `first_name`, `last_name`, `phone`, `created_at`, `is_active`) VALUES
(1,	'djole786@gmail.com',	'$2y$10$XuPOgTgAAAorjYS.1.NXTe2tIYgFmVNIK.JXEwL96Zdp6iZSyXShu',	'Devil',	NULL,	'0953628294',	'2025-12-04 23:04:47',	1),
(2,	'da@gmail.com',	'$2y$10$pdW7Vg7t7THHlB4aKrdKFuU1f0GD6RHYt4t9nQNyfnFMX91McK7BO',	'MojaBaba',	NULL,	'0995555555',	'2025-12-04 23:49:17',	1),
(4,	'djordje.vulevic123@gmail.com',	'$2y$10$wDQLwwGtBNIMfQ2s8EZ4reg/x8Z5rLkvcH6LJvOBLOS9TeFB4btoy',	'djordje',	'vulevic',	NULL,	'2026-02-02 02:10:45',	1),
(5,	'devilw530@gmail.com',	'$2y$10$iWmiGiT1y9w3Te2hhEv2Sud3niQPpmjwCejZvflHBcpDTcj1Dzumq',	'Devil',	'Warrior',	NULL,	'2026-02-02 18:55:54',	1),
(7,	'jozo@gmail.com',	'$2y$10$kX5UKKo6Xlj7bOSb2nM5hewlOz5OtIpd4Wy4ZP0F0e6fI6NibrVaO',	'Jozo',	NULL,	'0995555555',	'2026-02-18 04:17:38',	1),
(8,	'mojababa3@gmail.com',	'$2y$10$rNlkAGKNwMMCVn8L5r.5a.lp4Ghur9MdebMmGNaI3qLbXXlIBr/pq',	'MojaBaba3',	NULL,	'0995555555',	'2026-02-18 18:46:42',	1);

DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE `user_roles` (
  `user_role_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_role_id`),
  UNIQUE KEY `uniq_user_role` (`user_id`,`role_id`),
  KEY `idx_user_roles_user_id` (`user_id`),
  KEY `idx_user_roles_role_id` (`role_id`),
  CONSTRAINT `fk_user_roles_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_user_roles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 2026-02-18 19:04:28 UTC