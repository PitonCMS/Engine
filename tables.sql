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
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `role` char(1) NULL DEFAULT NULL,
  `created_by` int NOT NULL DEFAULT 1,
  `created_date` datetime NOT NULL,
  `updated_by` int NOT NULL DEFAULT 1,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_uq` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `page` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NULL DEFAULT NULL,
  `url` varchar(150) NOT NULL,
  `url_locked` char(1) NOT NULL DEFAULT 'N',
  `layout` varchar(60) NOT NULL,
  `meta_description` varchar(320) NULL DEFAULT NULL,
  `published_date` date NULL DEFAULT NULL,
  `created_by` int NOT NULL DEFAULT 1,
  `created_date` datetime NOT NULL,
  `updated_by` int NOT NULL DEFAULT 1,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url_uq` (`url`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `page_element` (
  `id` int NOT NULL AUTO_INCREMENT,
  `element_type` varchar(40) NULL DEFAULT NULL,
  `title` varchar(200) NULL DEFAULT NULL,
  `content_raw` mediumtext,
  `content` mediumtext,
  `collection_id` int NULL DEFAULT NULL,
  `media_id` int NULL DEFAULT NULL,
  `media_path` varchar(200) NULL DEFAULT NULL,
  `created_by` int NOT NULL DEFAULT 1,
  `created_date` datetime NOT NULL,
  `updated_by` int NOT NULL DEFAULT 1,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `page_section_element_map` (
  `id` int NOT NULL AUTO_INCREMENT,
  `page_id` int NOT NULL,
  `section_name` varchar(60) NOT NULL,
  `element_id` int NOT NULL,
  `element_sort` int NOT NULL DEFAULT 1,
  `created_by` int NOT NULL DEFAULT 1,
  `created_date` datetime NOT NULL,
  `updated_by` int NOT NULL DEFAULT 1,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id_idx` (`page_id`),
  KEY `section_name_idx` (`section_name`),
  KEY `element_id_idx` (`element_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `setting` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category` varchar(60) NOT NULL,
  `setting_key` varchar(40) NOT NULL,
  `setting_value` varchar(150) NULL DEFAULT NULL,
  `label` varchar(60) NULL DEFAULT NULL,
  `restricted` char(1) NOT NULL DEFAULT 'N',
  `created_by` int NOT NULL DEFAULT 1,
  `created_date` datetime NOT NULL,
  `updated_by` int NOT NULL DEFAULT 1,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_category_key_idx` (`category`, `setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

INSERT INTO `page` (`id`, `title`, `url`, `url_locked`, `layout`, `meta_description`, `published_date`, `created_by`, `created_date`, `updated_by`, `updated_date`)
  VALUES (1, 'Home', 'home', 'Y', 'home.html', 'All about this page for SEO.', NULL, 1, now(), 1, now());

INSERT INTO `page_element` (`id`, `element_type`, `title`, `content_raw`, `content`, `created_by`, `created_date`, `updated_by`, `updated_date`)
  VALUES
    (1, 'hero', 'First Section First Element', '# Element Content', '<h1>Element Content</h1>', 1, now(), 1, now()),
    (2, 'text', 'First Section Second Element', '# Element Content', '<h1>Element Content</h1>', 1, now(), 1, now());

INSERT INTO `page_section_element_map` (`id`, `page_id`, `section_name`, `element_id`, `element_sort`, `created_by`, `created_date`, `updated_by`, `updated_date`)
  VALUES
    (1, 1, 'hero', 1, 1, 1, now(), 1, now()),
    (2, 1, 'text', 2, 1, 1, now(), 1, now());

INSERT INTO `setting` (`category`, `setting_key`, `setting_value`, `label`, `created_by`, `created_date`, `updated_by`, `updated_date`)
  VALUES ('global', 'theme', 'default', 'Theme', 1, now(), 1, now());

SET FOREIGN_KEY_CHECKS=1;
