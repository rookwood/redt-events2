SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
USE `events`;

CREATE TABLE IF NOT EXISTS `characters` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(19) NOT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `visibility` tinyint(1) NOT NULL DEFAULT '1',
  `profession_id` int(11) unsigned NOT NULL,
  `race_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

CREATE TABLE IF NOT EXISTS `config` (
  `group_name` varchar(30) CHARACTER SET latin1 NOT NULL,
  `config_key` varchar(30) CHARACTER SET latin1 NOT NULL,
  `config_value` varchar(30) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `config` (`group_name`, `config_key`, `config_value`) VALUES
('registration', 'open_registration', 'b:1;'),
('registration', 'require_email_verification', 'b:0;'),
('lost_data', 'email_lost_password', 'b:0;');

CREATE TABLE IF NOT EXISTS `enrollment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(11) unsigned NOT NULL,
  `character_id` int(11) unsigned NOT NULL,
  `status_id` int(11) unsigned NOT NULL,
  `comment` text CHARACTER SET latin1,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`event_id`,`character_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `title` varchar(50) NOT NULL,
  `time` int(11) DEFAULT NULL,
  `location_id` int(11) unsigned NOT NULL,
  `description` text CHARACTER SET latin1,
  `player_limit` tinyint(3) unsigned NOT NULL,
  `status_id` int(11) unsigned NOT NULL,
  `character_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

CREATE TABLE IF NOT EXISTS `keys` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(128) CHARACTER SET latin1 NOT NULL,
  `action` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `sent_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `locations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `visibility` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

INSERT INTO `locations` (`id`, `name`, `visibility`) VALUES
(1, 'Ascalonian Catacombs (story)', 1),
(2, 'Ascalonian Catacombs (explorable)', 1),
(3, 'Caudecus''s Manor (story)', 1),
(4, 'Caudecus''s Manor (explorable)', 1),
(5, 'Twilight Arbor (story)', 1),
(6, 'Twilight Arbor (explorable)', 1),
(7, 'Sorrow''s Embrace (story)', 1),
(8, 'Sorrow''s Embrace (explorable)', 1),
(9, 'Citadel of Flame (story)', 1),
(10, 'Citadel of Flame (explorable)', 1),
(11, 'Honor of the Waves (story)', 1),
(12, 'Honor of the Waves (explorable)', 1),
(13, 'Crucible of Eternity (story)', 1),
(14, 'Crucible of Eternity (explorable)', 1),
(15, 'The Ruined City of Arah (story)', 1),
(16, 'The Ruined City of Arah (explorable)', 1),
(17, 'Eternal Battlegrounds', 1),
(18, 'Red Borderlands', 1),
(19, 'Green Borderlands', 1),
(20, 'Blue Borderlands', 1),
(21, 'WvW Location TBD', 1),
(22, 'Heart of the Mists', 1),
(23, 'Misc World PvE Zone', 1),
(24, 'Shenanigans Night', 1);
(25, 'Fractals of the Mists', 1);

CREATE TABLE IF NOT EXISTS `professions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(19) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

INSERT INTO `professions` (`id`, `name`) VALUES
(1, 'warrior'),
(2, 'guardian'),
(3, 'thief'),
(4, 'engineer'),
(5, 'ranger'),
(6, 'necromancer'),
(7, 'mesmer'),
(8, 'elementalist');

CREATE TABLE IF NOT EXISTS `profiles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(30) DEFAULT NULL,
  `last_name` varchar(45) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `from_gw1` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `profiles_ibfk_1` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

CREATE TABLE IF NOT EXISTS `races` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

INSERT INTO `races` (`id`, `name`) VALUES
(1, 'human'),
(2, 'sylvari'),
(3, 'char'),
(4, 'asura'),
(5, 'norn');

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'login', 'Login privileges, granted after account confirmation'),
(2, 'admin', 'Administrative user, has access to everything.'),
(3, 'verified', 'Email address verification complete'),
(4, 'officer', 'Guild officer'),
(5, 'leadership', 'Non-officer leadership role');

CREATE TABLE IF NOT EXISTS `roles_users` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `fk_role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sessions` (
  `session_id` varchar(24) CHARACTER SET latin1 NOT NULL,
  `last_active` int(10) unsigned NOT NULL,
  `contents` text CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_active` (`last_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `statuses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

INSERT INTO `statuses` (`id`, `name`) VALUES
(1, 'scheduled'),
(2, 'cancelled'),
(3, 'ready'),
(4, 'stand-by (forced)'),
(5, 'stand-by (voluntary)');

CREATE TABLE IF NOT EXISTS `user_tokens` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `user_agent` varchar(40) NOT NULL,
  `token` varchar(40) NOT NULL,
  `type` varchar(100) NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_token` (`token`),
  KEY `fk_user_id` (`user_id`),
  KEY `expires` (`expires`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET utf8 NOT NULL,
  `password` varchar(64) CHARACTER SET utf8 NOT NULL,
  `username` varchar(32) CHARACTER SET utf8 NOT NULL,
  `timezone` varchar(50) CHARACTER SET utf8 NOT NULL,
  `logins` int(10) unsigned NOT NULL,
  `last_login` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

ALTER TABLE `profiles`
  ADD CONSTRAINT `profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `roles_users`
  ADD CONSTRAINT `roles_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_users_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

ALTER TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;