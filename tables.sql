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
  `time_updated` int DEFAULT NULL,
  PRIMARY KEY (`session_id`),
  KEY `time_updated_idx` (`time_updated`)
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
  UNIQUE KEY `url_uq` (`url`),
  KEY `published_date_idx` (`published_date`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `page_element` (
  `id` int NOT NULL AUTO_INCREMENT,
  `page_id` int NOT NULL,
  `section_name` varchar(60) NOT NULL,
  `element_type` varchar(40) NULL DEFAULT NULL,
  `element_sort` int NOT NULL DEFAULT 1,
  `title` varchar(200) NULL DEFAULT NULL,
  `content_raw` mediumtext,
  `content` mediumtext,
  `excerpt` varchar(60) NULL DEFAULT NULL,
  `collection_id` int NULL DEFAULT NULL,
  `gallery_id` int NULL DEFAULT NULL,
  `image_path` varchar(100) NULL DEFAULT NULL,
  `video_path` varchar(1000) NULL DEFAULT NULL,
  `created_by` int NOT NULL DEFAULT 1,
  `created_date` datetime NOT NULL,
  `updated_by` int NOT NULL DEFAULT 1,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id_idx` (`page_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(60) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 1,
  `setting_key` varchar(40) NOT NULL,
  `setting_value` varchar(1000) DEFAULT NULL,
  `input_type` varchar(20) DEFAULT NULL,
  `label` varchar(60) DEFAULT NULL,
  `help` varchar(500) DEFAULT NULL,
  `restricted` char(1) NOT NULL DEFAULT 'N',
  `created_by` int(11) NOT NULL DEFAULT 1,
  `created_date` datetime NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT 1,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `setting_key_uq` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4;

INSERT INTO `page` (`id`, `title`, `url`, `url_locked`, `layout`, `meta_description`, `published_date`, `created_by`, `created_date`, `updated_by`, `updated_date`)
  VALUES (1, 'Home', 'home', 'Y', 'home.html', 'All about this page for SEO.', NULL, 1, now(), 1, now());

INSERT INTO `page_element` (`id`, `page_id`, `section_name`, `element_type`, `element_sort`, `title`, `content_raw`, `content`, `image_path`, `created_by`, `created_date`, `updated_by`, `updated_date`)
  VALUES
    (1, 1, 'aboveTheFoldHero', 'hero', 1, 'Hero Image', 'Call to Action!', '<p>Call to Action!</p>', 'https://unsplash.it/600', 1, now(), 1, now()),
    (2, 1, 'introBlock', 'text', 1, 'Main Body Text', 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.', '<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>', null, 1, now(), 1, now());

INSERT INTO `setting` (`id`, `category`, `sort_order`, `setting_key`, `setting_value`, `input_type`, `label`, `help`, `restricted`, `created_by`, `created_date`, `updated_by`, `updated_date`)
VALUES
  (1,'site',1,'theme','default','select','Theme',NULL,'N',1,now(),1,now()),
  (2,'site',2,'dateFormat','mm/dd/yyyy','select','Date Format','Select date picker format to use across site.','N',1,now(),1,now()),
  (3,'site',3,'googleWebMaster','Unique GWM Key',NULL,'Google Webmaster Verification Link',NULL,'N',1,now(),1,now()),
  (4,'site',4,'googleAnalytics','Google Analytics Tracking Code',NULL,'Google Analytics Code',NULL,'N',1,now(),1,now()),
  (5,'site',5,'statCounter','Stat counter code',NULL,'Stat Counter',NULL,'N',1,now(),1,now()),
  (6,'contact',1,'displayName','Moritz Media',NULL,'Display Name',NULL,'N',1,now(),1,now()),
  (7,'contact',2,'telephone','555-1212',NULL,'Telephone',NULL,'N',1,now(),1,now()),
  (8,'contact',3,'mobile','541-555-1212',NULL,'Mobile',NULL,'N',1,now(),1,now()),
  (9,'contact',4,'address1','Building 4',NULL,'Address Line 1',NULL,'N',1,now(),1,now()),
  (10,'contact',5,'address2','1234 Main St',NULL,'Address Line 2',NULL,'N',1,now(),1,now()),
  (11,'contact',6,'address3','Flat 13',NULL,'Address Line 3',NULL,'N',1,now(),1,now()),
  (12,'contact',7,'city','Bend',NULL,'City',NULL,'N',1,now(),1,now()),
  (13,'contact',8,'province','OR',NULL,'State',NULL,'N',1,now(),1,now()),
  (14,'contact',9,'postalCode','TW1 2HU',NULL,'Postal Code',NULL,'N',1,now(),1,now()),
  (15,'contact',10,'country','United States',NULL,'Country',NULL,'N',1,now(),1,now()),
  (16,'social',1,'facebookLink','https://www.facebook.com/',NULL,'Facebook Link',NULL,'N',1,now(),1,now()),
  (17,'social',2,'twitterLink','https://twitter.com',NULL,'Twitter Link',NULL,'N',1,now(),1,now()),
  (18,'social',3,'instagramLink','https://www.instagram.com/',NULL,'Instagram Link',NULL,'N',1,now(),1,now()),
  (19,'social',4,'linkedinLink','https://www.linkedin.com/',NULL,'LinkedIn Link',NULL,'N',1,now(),1,now()),
  (20,'social',5,'githubLink','https://www.github.com/',NULL,'GitHub Link',NULL,'N',1,now(),1,now());

