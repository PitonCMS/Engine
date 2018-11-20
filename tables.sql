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
  `role` char(1) NULL DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT '1',
  `created_date` datetime NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '1',
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_uq` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sort` int(11) NULL DEFAULT '1',
  `name` varchar(60) NULL DEFAULT NULL,
  `title` varchar(60) NULL DEFAULT NULL,
  `url` varchar(150) NOT NULL,
  `url_locked` enum('N','Y') NOT NULL DEFAULT 'N',
  `meta_description` varchar(320) NULL DEFAULT NULL,
  `template` varchar(60) DEFAULT NULL,
  `restricted` enum('N','Y') NOT NULL DEFAULT 'N',
  `created_by` int(11) NOT NULL DEFAULT '1',
  `created_date` datetime NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '1',
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url_uq` (`url`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `page_section` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `sort` int(11) NULL DEFAULT '1',
  `name` varchar(60) NOT NULL,
  `title` varchar(60) NULL DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT '1',
  `created_date` datetime NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '1',
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id_idx` (`page_id`),
  UNIQUE KEY `page_section_name_uq` (`page_id`, `name`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `page_element` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_section_id` int(11) NOT NULL,
  `sort` int(11) NULL DEFAULT '1',
  `name` varchar(60) NOT NULL,
  `title` varchar(60) NULL DEFAULT NULL,
  `content_raw` text,
  `content` text,
  `created_by` int(11) NOT NULL DEFAULT '1',
  `created_date` datetime NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '1',
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_section_id_idx` (`page_section_id`),
  UNIQUE KEY `page_section_id_name_uq` (`page_section_id`, `name`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(60) NOT NULL,
  `setting_key` varchar(40) NOT NULL,
  `setting_value` varchar(150) NULL DEFAULT NULL,
  `label` varchar(60) NULL DEFAULT NULL,
  `restricted` enum('N','Y') NOT NULL DEFAULT 'N',
  `created_by` int(11) NOT NULL DEFAULT '1',
  `created_date` datetime NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT '1',
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_category_key_idx` (`category`, `setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

ALTER TABLE `page_section`
ADD CONSTRAINT `page_section_page_id_fk` FOREIGN KEY (`page_id`) REFERENCES `page` (`id`) ON DELETE CASCADE;

ALTER TABLE `page_element`
ADD CONSTRAINT `page_element_section_id_fk` FOREIGN KEY (`page_section_id`) REFERENCES `page_section` (`id`) ON DELETE CASCADE;

INSERT INTO `page` (`id`, `sort`, `name`, `title`, `url`, `url_locked`, `meta_description`, `template`, `restricted`, `created_by`, `created_date`, `updated_by`, `updated_date`)
  VALUES (1, 1, 'home', 'Home', 'home', 'Y', 'All about this page for SEO.', 'home.html', 'Y', 1, now(), 1, now());

INSERT INTO `page_section` (`id`, `page_id`, `sort`, `name`, `title`, `created_by`, `created_date`, `updated_by`, `updated_date`)
  VALUES
    (1, 1, 1, 'section1', 'First Section', 1, now(), 1, now()),
    (2, 1, 2, 'section2', 'Second Section', 1, now(), 1, now());

INSERT INTO `page_element` (`id`, `page_section_id`, `sort`, `name`, `title`, `content_raw`, `content`, `created_by`, `created_date`, `updated_by`, `updated_date`)
  VALUES
    (1, 1, 1, 'element1', 'First Section First Element', '# Element Content', '<h1>Element Content</h1>', 1, now(), 1, now()),
    (2, 1, 2, 'element2', 'First Section Second Element', '# Element Content', '<h1>Element Content</h1>', 1, now(), 1, now()),
    (3, 2, 1, 'element1', 'Second Section First Element', '# Element Content', '<h1>Element Content</h1>', 1, now(), 1, now()),
    (4, 2, 2, 'element2', 'Second Section Second Element', '# Element Content', '<h1>Element Content</h1>', 1, now(), 1, now());

INSERT INTO `setting` (`category`, `setting_key`, `setting_value`, `label`, `created_by`, `created_date`, `updated_by`, `updated_date`)
  VALUES ('global', 'theme', 'default', 'Theme', 1, now(), 1, now());

SET FOREIGN_KEY_CHECKS=1;
