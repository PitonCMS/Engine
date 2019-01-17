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
  `collection_id` int NULL DEFAULT NULL,
  `slug` varchar(100) NOT NULL,
  `definition` varchar(60) NULL DEFAULT NULL,
  `template` varchar(60) NOT NULL,
  `title` varchar(60) NULL DEFAULT NULL,
  `sub_title` varchar(150) NULL DEFAULT NULL,
  `meta_description` varchar(320) NULL DEFAULT NULL,
  `published_date` date NULL DEFAULT NULL,
  `created_by` int NOT NULL DEFAULT 1,
  `created_date` datetime NOT NULL,
  `updated_by` int NOT NULL DEFAULT 1,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `collection_id_idx` (`collection_id`),
  UNIQUE KEY `slug_uq` (`slug`,`id`),
  KEY `published_date_idx` (`published_date`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `page_element` (
  `id` int NOT NULL AUTO_INCREMENT,
  `page_id` int NOT NULL,
  `block_key` varchar(60) NOT NULL,
  `template` varchar(60) NOT NULL
  `element_type` varchar(40) NULL DEFAULT NULL,
  `element_sort` int NOT NULL DEFAULT 1,
  `title` varchar(200) NULL DEFAULT NULL,
  `content_raw` mediumtext NULL DEFAULT NULL,
  `content` mediumtext NULL DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `collection` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `definition` varchar(60) NOT NULL,
  `created_by` int NOT NULL DEFAULT 1,
  `created_date` datetime NOT NULL,
  `updated_by` int NOT NULL DEFAULT 1,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug_uq` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `setting` (
  `id` int NOT NULL AUTO_INCREMENT,
  `page_id` int NULL DEFAULT NULL,
  `scope` varchar(20) NOT NULL,
  `category` varchar(60) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 1,
  `setting_key` varchar(60) NOT NULL,
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
  KEY `page_id_idx` (`page_id`),
  KEY `setting_key_uq` (`scope`,`page_id`,`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

INSERT INTO `page` (`id`, `collection_id`, `slug`, `definition`, `template`, `title`, `sub_title`, `meta_description`, `published_date`, `created_by`, `created_date`, `updated_by`, `updated_date`)
VALUES
  (1,NULL,'home','home.json','home.html','Home',NULL,'All about this page for SEO.','2018-12-27',1,'2018-12-22 11:32:10',1,'2018-12-29 13:28:25'),
  (2,NULL,'styles','style.json','style.html','Styles',NULL,'Style Guide for Default Theme PITONcms','2018-12-27',1,'2018-12-22 14:08:02',1,'2019-01-11 14:46:03'),
  (3,NULL,'gallery','gallery.json','gallery.html','Gallery',NULL,'Gallery ','2018-12-29',1,'2018-12-29 14:12:38',1,'2019-01-11 14:45:49'),
  (4,NULL,'video','video.json','video.html','Video',NULL,'Video Layout Page','2019-01-10',1,'2019-01-11 14:27:16',1,'2019-01-11 14:53:08');

INSERT INTO `page_element` (`id`, `page_id`, `block_key`, `template`, `element_type`, `element_sort`, `title`, `content_raw`, `content`, `excerpt`, `collection_id`, `gallery_id`, `image_path`, `video_path`, `created_by`, `created_date`, `updated_by`, `updated_date`)
VALUES
  (1,1,'aboveTheFoldHero','hero.html','hero',1,'Hero Image','<button>Call to Action!</button>','<button>Call to Action!</button>','Call to Action!',NULL,NULL,'https://unsplash.it/1920/500',NULL,1,'2018-12-22 11:32:10',1,'2018-12-29 13:28:25'),
  (2,1,'introBlock','text.html','text',1,'Main Body Text','Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.\r\n\r\nhttps:// source.unsplash .com/random/1920x450','<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>\n<p>https:// source.unsplash .com/random/1920x450</p>','Sed ut perspiciatis unde omnis iste natus error sit',NULL,NULL,NULL,NULL,1,'2018-12-22 11:32:10',1,'2018-12-29 13:28:25'),
  (3,2,'heroGuide','hero.html','hero',2,'PitonCMS Hero','A full width image hero','<p>A full width image hero</p>','A full width image hero',NULL,NULL,'https://unsplash.it/2001/1000',NULL,1,'2018-12-22 14:08:02',1,'2019-01-11 14:46:03'),
  (4,2,'typoGuide','text.html','text',1,'Typography','<p>This is an example of plain text</p>\r\n<p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Reiciendis sit provident quas quia deserunt, officiis repellendus vel praesentium esse eius iure perferendis sunt id fugiat quis nemo nobis incidunt? Ad temporibus sit impedit! Repudiandae, placeat dolore consequuntur ratione aspernatur qui veritatis a atque. Quasi delectus, numquam odit quisquam odio minus blanditiis rem fugiat voluptate quaerat quam magnam atque nam.</p>\r\n\r\n<h1>Heading 1</h1>\r\n<h2>Heading 2</h2>\r\n<h3>Heading 3</h3>\r\n<h4>Heading 4</h4>\r\n<h5>Heading 5</h5>\r\n<h6>Heading 6</h6>','<p>This is an example of plain text</p>\n<p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Reiciendis sit provident quas quia deserunt, officiis repellendus vel praesentium esse eius iure perferendis sunt id fugiat quis nemo nobis incidunt? Ad temporibus sit impedit! Repudiandae, placeat dolore consequuntur ratione aspernatur qui veritatis a atque. Quasi delectus, numquam odit quisquam odio minus blanditiis rem fugiat voluptate quaerat quam magnam atque nam.</p>\n<h1>Heading 1</h1>\n<h2>Heading 2</h2>\n<h3>Heading 3</h3>\n<h4>Heading 4</h4>\n<h5>Heading 5</h5>\n<h6>Heading 6</h6>','This is an example of plain text Lorem ipsum dolor, sit',NULL,NULL,NULL,NULL,1,'2018-12-22 14:13:56',1,'2019-01-11 14:46:03'),
  (5,2,'galleryGuide','gallery.html','gallery',1,'Gallery Style Guide','A gallery of images.','<p>A gallery of images.</p>','A gallery of images.',NULL,NULL,NULL,NULL,1,'2018-12-22 14:19:28',1,'2019-01-11 14:46:03'),
  (6,2,'collectionGuide','collection.html','collection',1,'Collection Element','This is an element that requires a collection to be created and then selected from the dropdown below. This is for when you have data that needs summary/detail.','<p>This is an element that requires a collection to be created and then selected from the dropdown below. This is for when you have data that needs summary/detail.</p>','This is an element that requires a collection to be created',NULL,NULL,NULL,NULL,1,'2018-12-22 22:21:27',1,'2019-01-11 14:46:03'),
  (7,2,'imageGuide','image.html','image',1,'Image Element','For when you need to add an image element into a website. The image loading below is 600 x 400 px','<p>For when you need to add an image element into a website. The image loading below is 600 x 400 px</p>','For when you need to add an image element into a website.',NULL,NULL,'https://unsplash.it/1000/400',NULL,1,'2018-12-22 22:24:44',1,'2019-01-11 14:46:03'),
  (8,2,'videoGuide','video.html','video',1,'Video Element - Vimeo','For when you want to embed an external link that comes with its own embed code like an iframe that is used by both YouTube and Vimeo.\r\n\r\nThis is a Vimeo link using responsive settings from Vimeo.','<p>For when you want to embed an external link that comes with its own embed code like an iframe that is used by both YouTube and Vimeo.</p>\n<p>This is a Vimeo link using responsive settings from Vimeo.</p>','For when you want to embed an external link that comes with',NULL,NULL,NULL,'<iframe src=\"https://player.vimeo.com/video/259291697\" width=\"640\" height=\"360\" frameborder=\"0\" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>\r\n<p><a href=\"https://vimeo.com/259291697\">Travel Oregon - Only Slightly Exaggerated</a> from <a href=\"https://vimeo.com/suncreature\">Sun Creature Studio</a> on <a href=\"https://vimeo.com\">Vimeo</a>.</p>',1,'2018-12-22 22:27:38',1,'2019-01-11 14:46:03'),
  (9,2,'videoGuide','video.html','video',1,'Video Element - Youtube','This is a YouTube embed link.','<p>This is a YouTube embed link.</p>','This is a YouTube embed link.',NULL,NULL,NULL,'<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/Fllx5pzvfFQ\" frameborder=\"0\" allow=\"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>',1,'2018-12-22 22:33:55',1,'2019-01-11 14:46:03'),
  (10,3,'galleryGuide','gallery.htl','gallery',1,'Piton Gallery','This page represents the default gallery page. A gallery will need to be created before this page can be created. Currently it is populated with images from Unsplash.','<p>This page represents the default gallery page. A gallery will need to be created before this page can be created. Currently it is populated with images from Unsplash.</p>','This page represents the default gallery page. A gallery',NULL,NULL,NULL,NULL,1,'2018-12-29 14:12:38',1,'2019-01-11 14:45:49'),
  (11,4,'videoGuide','video.html','video',1,'Style Guide for Video','This is how the video will appear.','<p>This is how the video will appear.</p>','This is how the video will appear.',NULL,NULL,NULL,'<iframe width=\"560\" height=\"315\" src=\"https://www.youtube-nocookie.com/embed/Y7RAqOaj_JE\" frameborder=\"0\" allow=\"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>',1,'2019-01-11 14:27:16',1,'2019-01-11 14:53:08');

INSERT INTO `setting` (`scope`, `category`, `sort_order`, `setting_key`, `setting_value`, `input_type`, `label`, `help`, `restricted`, `created_by`, `created_date`, `updated_by`, `updated_date`)
VALUES
  ('global','site',1,'theme','default','select','Theme',NULL,'N',1,now(),1,now()),
  ('global','site',2,'urlDomainName','example.com','','Domain Name','Do not include the http(s):// or a trailing slash. For use in generated sitemaps.','N',1,now(),1,now()),
  ('global','site',3,'urlScheme','http','select','URL Scheme','Select <code>http</code> or <code>https</code> (https requires additional server changes).','N',1,now(),1,now()),
  ('global','site',4,'dateFormat','mm/dd/yyyy','select','Date Format','Select date picker format to use across site.','N',1,now(),1,now()),
  ('global','site',5,'googleWebMaster','Unique GWM Key',NULL,'Google Webmaster Verification Link',NULL,'N',1,now(),1,now()),
  ('global','site',6,'googleAnalytics','Google Analytics Tracking Code',NULL,'Google Analytics Code',NULL,'N',1,now(),1,now()),
  ('global','site',7,'statCounter','Stat counter code',NULL,'Stat Counter',NULL,'N',1,now(),1,now()),
  ('global','contact',1,'displayName','Moritz Media',NULL,'Display Name',NULL,'N',1,now(),1,now()),
  ('global','contact',2,'telephone','555-1212',NULL,'Telephone',NULL,'N',1,now(),1,now()),
  ('global','contact',3,'mobile','541-555-1212',NULL,'Mobile',NULL,'N',1,now(),1,now()),
  ('global','contact',4,'address1','Building 4',NULL,'Address Line 1',NULL,'N',1,now(),1,now()),
  ('global','contact',5,'address2','1234 Main St',NULL,'Address Line 2',NULL,'N',1,now(),1,now()),
  ('global','contact',6,'address3','Flat 13',NULL,'Address Line 3',NULL,'N',1,now(),1,now()),
  ('global','contact',7,'city','Bend',NULL,'City',NULL,'N',1,now(),1,now()),
  ('global','contact',8,'province','OR',NULL,'State',NULL,'N',1,now(),1,now()),
  ('global','contact',9,'postalCode','TW1 2HU',NULL,'Postal Code',NULL,'N',1,now(),1,now()),
  ('global','contact',10,'country','United States',NULL,'Country',NULL,'N',1,now(),1,now()),
  ('global','social',1,'facebookLink','https://www.facebook.com/',NULL,'Facebook Link',NULL,'N',1,now(),1,now()),
  ('global','social',2,'twitterLink','https://twitter.com',NULL,'Twitter Link',NULL,'N',1,now(),1,now()),
  ('global','social',3,'instagramLink','https://www.instagram.com/',NULL,'Instagram Link',NULL,'N',1,now(),1,now()),
  ('global','social',4,'linkedinLink','https://www.linkedin.com/',NULL,'LinkedIn Link',NULL,'N',1,now(),1,now()),
  ('global','social',5,'githubLink','https://www.github.com/',NULL,'GitHub Link',NULL,'N',1,now(),1,now());

