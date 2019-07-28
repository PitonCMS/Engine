SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `session` (
  `session_id` char(64) NOT NULL,
  `data` text,
  `user_agent` char(64) DEFAULT NULL,
  `ip_address` varchar(46) DEFAULT NULL,
  `time_updated` int DEFAULT NULL,
  PRIMARY KEY (`session_id`),
  KEY `time_updated_idx` (`time_updated`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(60) NULL DEFAULT NULL,
  `last_name` varchar(60) NULL DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `role` char(1) NULL DEFAULT NULL,
  `active` enum('Y', 'N') NOT NULL DEFAULT 'Y',
  `created_by` int NOT NULL DEFAULT 1,
  `created_date` datetime NOT NULL,
  `updated_by` int NOT NULL DEFAULT 1,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_uq` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `page` (
  `id` int NOT NULL AUTO_INCREMENT,
  `collection_slug` varchar(100) NULL DEFAULT NULL,
  `page_slug` varchar(100) NOT NULL,
  `definition` varchar(60) NOT NULL,
  `template` varchar(60) NOT NULL,
  `title` varchar(60) NOT NULL,
  `sub_title` varchar(150) NULL DEFAULT NULL,
  `meta_description` varchar(320) NULL DEFAULT NULL,
  `published_date` date NULL DEFAULT NULL,
  `image_path` varchar(100) NULL DEFAULT NULL,
  `created_by` int NOT NULL DEFAULT 1,
  `created_date` datetime NOT NULL,
  `updated_by` int NOT NULL DEFAULT 1,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_slug_idx` (`page_slug`),
  UNIQUE KEY `slug_uq` (`collection_slug`,`page_slug`),
  KEY `published_date_idx` (`published_date`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `page_element` (
  `id` int NOT NULL AUTO_INCREMENT,
  `page_id` int NOT NULL,
  `block_key` varchar(60) NOT NULL,
  `definition` varchar(60) NULL DEFAULT NULL,
  `template` varchar(60) NOT NULL,
  `element_sort` int NOT NULL DEFAULT 1,
  `title` varchar(200) NULL DEFAULT NULL,
  `content_raw` mediumtext NULL DEFAULT NULL,
  `content` mediumtext NULL DEFAULT NULL,
  `excerpt` varchar(60) NULL DEFAULT NULL,
  `collection_slug` varchar(100) NULL DEFAULT NULL,
  `gallery_id` int NULL DEFAULT NULL,
  `image_path` varchar(100) NULL DEFAULT NULL,
  `embedded` varchar(1000) NULL DEFAULT NULL,
  `created_by` int NOT NULL DEFAULT 1,
  `created_date` datetime NOT NULL,
  `updated_by` int NOT NULL DEFAULT 1,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id_idx` (`page_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `navigation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `navigator` varchar(60) NOT NULL DEFAULT 'main',
  `parent_id` int NULL DEFAULT NULL,
  `sort` smallint NULL DEFAULT 1,
  `page_id` int NULL DEFAULT NULL,
  `title` varchar(60) NULL DEFAULT NULL,
  `active` enum('Y', 'N') NOT NULL DEFAULT 'Y',
  `created_by` int NOT NULL DEFAULT 1,
  `created_date` datetime NOT NULL,
  `updated_by` int NOT NULL DEFAULT 1,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `navigator_idx` (`navigator`),
  KEY `page_id_idx` (`page_id`),
  KEY `parent_id_idx` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `setting` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category` varchar(60) NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `setting_key` varchar(60) NOT NULL,
  `setting_value` varchar(4000) DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 1,
  `created_date` datetime NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT 1,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `setting_category_idx` (`category`),
  KEY `setting_ref_cat_idx` (`reference_id`, `category`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `media` (
  `id` int NOT NULL AUTO_INCREMENT,
  `filename` varchar(20) NOT NULL,
  `width` int NULL DEFAULT NULL,
  `height` int NULL DEFAULT NULL,
  `feature` enum('Y', 'N') NOT NULL DEFAULT 'N',
  `caption` varchar(100) NULL DEFAULT NULL,
  `created_by` int NOT NULL DEFAULT 1,
  `created_date` datetime NOT NULL,
  `updated_by` int NOT NULL DEFAULT 1,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `media_category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category` varchar(100) NOT NULL,
  `created_by` int NOT NULL DEFAULT 1,
  `created_date` datetime NOT NULL,
  `updated_by` int NOT NULL DEFAULT 1,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_idx` (`category`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `media_category_map` (
  `media_id` int NOT NULL,
  `category_id` int NOT NULL,
  UNIQUE KEY `media_cat_uq` (`media_id`, `category_id`),
  KEY `category_id_idx` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `message` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NULL DEFAULT NULL,
  `email` varchar(100) NULL DEFAULT NULL,
  `message` varchar(1000) NULL DEFAULT NULL,
  `is_read` enum('Y', 'N') NOT NULL DEFAULT 'N',
  `created_by` int NOT NULL DEFAULT 1,
  `created_date` datetime NOT NULL,
  `updated_by` int NOT NULL DEFAULT 1,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `message_read_idx` (`is_read`),
  KEY `message_date_idx` (`created_date`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

INSERT INTO `page` (`id`, `collection_slug`, `page_slug`, `definition`, `template`, `title`, `sub_title`, `meta_description`, `published_date`, `image_path`, `created_by`, `created_date`, `updated_by`, `updated_date`)
VALUES
  (1,NULL,'home','home.json','home.html','Home',NULL,'All about this page for SEO.','2018-12-27',NULL,1,now(),1,now()),;

INSERT INTO `page_element` (`id`, `page_id`, `block_key`, `definition`, `template`, `element_sort`, `title`, `content_raw`, `content`, `excerpt`, `collection_slug`, `gallery_id`, `image_path`, `embedded`, `created_by`, `created_date`, `updated_by`, `updated_date`)
VALUES
  (1,1,'aboveTheFoldHero','hero.json','hero.html',1,'Welcome to PitonCMS','A flexible content management system for your personal website.','<p>A flexible content management system for your personal website.</p>','A flexible content management system for your personal',NULL,NULL,NULL,NULL,1,now(),1,now()),
  (2,1,'introBlock','text.json','text.html',1,'Where to Start?','Congratulations! You have successfully installed PitonCMS. \r\n\r\nTo start, you will want to read the documentation on how to setup and configure your new site <a href=\"https://github.com/pitoncms\" target=\"_blank\">here</a>. Follow the easy step-by-step process for creating your own personalized theme.  \r\n\r\n','<p>Congratulations! You have successfully installed PitonCMS. </p>\n<p>To start, you will want to read the documentation on how to setup and configure your new site <a href=\"https://github.com/pitoncms\" target=\"_blank\">here</a>. Follow the easy step-by-step process for creating your own personalized theme.  </p>','Congratulations! You have successfully installed PitonCMS.',NULL,NULL,NULL,NULL,1,now(),1,now());

INSERT INTO `setting` (`category`,`reference_id`, `setting_key`, `setting_value`, `created_by`, `created_date`, `updated_by`, `updated_date`)
VALUES
  ('page',1,'ctaTitle','Read more on Github',1,now(),1,now()),
  ('page',1,'ctaTarget','https://github.com/pitoncms',1,now(),1,now());
