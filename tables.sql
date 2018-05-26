SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `pitoncms`;
CREATE USER IF NOT EXISTS 'pitoncms'@'localhost' IDENTIFIED BY 'pitoncmspassword';
GRANT ALL ON `pitoncms`.* TO 'pitoncms'@'localhost' IDENTIFIED BY 'pitoncmspassword';

USE `pitoncms`;

CREATE TABLE IF NOT EXISTS `session` (
  `session_id` char(64) NOT NULL,
  `data` text,
  `user_agent` char(64) DEFAULT NULL,
  `ip_address` varchar(46) DEFAULT NULL,
  `time_updated` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(60) NOT NULL,
  `created_by` int(11) NOT NULL DEFAULT '1',
  `created_date` datetime NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '1',
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_uq` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL,
  `url` varchar(150) NOT NULL,
  `url_locked` enum('N','Y') NOT NULL DEFAULT 'N',
  `meta_description` varchar(320) NULL DEFAULT NULL,
  `sort` int(11) NULL DEFAULT NULL,
  `template` varchar(60) DEFAULT NULL,
  `deletable` enum('N','Y') NOT NULL DEFAULT 'Y',
  `created_by` int(11) NOT NULL DEFAULT '1',
  `created_date` datetime NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '1',
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url_uq` (`url`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `page_element` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `content_raw` text,
  `content` text,
  `created_by` int(11) NOT NULL DEFAULT '1',
  `created_date` datetime NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '1',
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id_idx` (`page_id`),
  UNIQUE KEY `page_id_name_uq` (`page_id`, `name`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(60) NOT NULL,
  `setting_key` varchar(40) NOT NULL,
  `setting_value` varchar(150) NULL DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT '1',
  `created_date` datetime NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '1',
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_category_key_idx` (`category`, `setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

ALTER TABLE `page_element`
ADD CONSTRAINT `page_element_page_id_fk` FOREIGN KEY (`page_id`) REFERENCES `page` (`id`) ON DELETE CASCADE;

INSERT INTO `page` (`title`, `url`, `url_locked`, `template`, `deletable`, `created_by`, `created_date`, `updated_by`, `updated_date`)
  VALUES ('Home', 'home', 'Y', 'home.html', 'N', 1, now(), 1, now());

INSERT INTO `setting` (`category`, `setting_key`, `setting_value`, `created_by`, `created_date`, `updated_by`, `updated_date`)
  VALUES ('site', 'theme', 'default', 1, now(), 1, now());

SET FOREIGN_KEY_CHECKS=1;
