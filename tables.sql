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
  `slug` varchar(100) NOT NULL,
  `layout` varchar(60) NOT NULL,
  `meta_description` varchar(320) NULL DEFAULT NULL,
  `published_date` date NULL DEFAULT NULL,
  `created_by` int NOT NULL DEFAULT 1,
  `created_date` datetime NOT NULL,
  `updated_by` int NOT NULL DEFAULT 1,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug_uq` (`slug`),
  KEY `published_date_idx` (`published_date`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `page_element` (
  `id` int NOT NULL AUTO_INCREMENT,
  `page_id` int NOT NULL,
  `section_name` varchar(60) NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `collection` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `layout` varchar(60) NULL DEFAULT NULL,
  `kind` varchar(60) NULL DEFAULT NULL,
  `created_by` int NOT NULL DEFAULT 1,
  `created_date` datetime NOT NULL,
  `updated_by` int NOT NULL DEFAULT 1,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug_uq` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `collection_detail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `collection_id` int NOT NULL,
  `sort` int NULL DEFAULT NULL,
  `slug` varchar(100) NOT NULL,
  `layout` varchar(60) NULL DEFAULT NULL,
  `title` varchar(60) NULL DEFAULT NULL,
  `sub_title` varchar(250) NULL DEFAULT NULL,
  `content_raw` mediumtext NULL DEFAULT NULL,
  `content` mediumtext NULL DEFAULT NULL,
  `expansion` mediumtext NULL DEFAULT NULL,
  `summary_image_path` varchar(100) NULL DEFAULT NULL,
  `detail_image_path` varchar(100) NULL DEFAULT NULL,
  `published_date` date NULL DEFAULT NULL,
  `created_by` int NOT NULL DEFAULT 1,
  `created_date` datetime NOT NULL,
  `updated_by` int NOT NULL DEFAULT 1,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `collection_id_idx` (`collection_id`),
  KEY `slug_idx` (`slug`),
  KEY `published_date_idx` (`published_date`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  KEY `setting_key_uq` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

INSERT INTO `page` (`id`, `title`, `slug`, `layout`, `meta_description`, `published_date`, `created_by`, `created_date`, `updated_by`, `updated_date`)
VALUES
  (1,'Home','home','home.html','All about this page for SEO.','2018-12-27',1,'2018-12-22 11:32:10',1,'2018-12-29 13:28:25'),
  (2,'Styles','styles','style.html','Style Guide for Default Theme PITONcms','2018-12-27',1,'2018-12-22 14:08:02',1,'2019-01-11 14:46:03'),
  (3,'Gallery','gallery','gallery.html','Gallery ','2018-12-29',1,'2018-12-29 14:12:38',1,'2019-01-11 14:45:49'),
  (4,'Video','video','video.html','Video Layout Page','2019-01-10',1,'2019-01-11 14:27:16',1,'2019-01-11 14:53:08');

INSERT INTO `page_element` (`id`, `page_id`, `section_name`, `element_type`, `element_sort`, `title`, `content_raw`, `content`, `excerpt`, `collection_id`, `gallery_id`, `image_path`, `video_path`, `created_by`, `created_date`, `updated_by`, `updated_date`)
VALUES
  (1,1,'aboveTheFoldHero','hero',1,'Hero Image','<button>Call to Action!</button>','<button>Call to Action!</button>','Call to Action!',NULL,NULL,'https://unsplash.it/1920/500',NULL,1,'2018-12-22 11:32:10',1,'2018-12-29 13:28:25'),
  (2,1,'introBlock','text',1,'Main Body Text','Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.\r\n\r\nhttps:// source.unsplash .com/random/1920x450','<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>\n<p>https:// source.unsplash .com/random/1920x450</p>','Sed ut perspiciatis unde omnis iste natus error sit',NULL,NULL,NULL,NULL,1,'2018-12-22 11:32:10',1,'2018-12-29 13:28:25'),
  (12,2,'heroGuide','hero',2,'PitonCMS Hero','A full width image hero','<p>A full width image hero</p>','A full width image hero',NULL,NULL,'https://unsplash.it/2001/1000',NULL,1,'2018-12-22 14:08:02',1,'2019-01-11 14:46:03'),
  (14,2,'typoGuide','text',1,'Typography','<p>This is an example of plain text</p>\r\n<p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Reiciendis sit provident quas quia deserunt, officiis repellendus vel praesentium esse eius iure perferendis sunt id fugiat quis nemo nobis incidunt? Ad temporibus sit impedit! Repudiandae, placeat dolore consequuntur ratione aspernatur qui veritatis a atque. Quasi delectus, numquam odit quisquam odio minus blanditiis rem fugiat voluptate quaerat quam magnam atque nam.</p>\r\n\r\n<h1>Heading 1</h1>\r\n<h2>Heading 2</h2>\r\n<h3>Heading 3</h3>\r\n<h4>Heading 4</h4>\r\n<h5>Heading 5</h5>\r\n<h6>Heading 6</h6>','<p>This is an example of plain text</p>\n<p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Reiciendis sit provident quas quia deserunt, officiis repellendus vel praesentium esse eius iure perferendis sunt id fugiat quis nemo nobis incidunt? Ad temporibus sit impedit! Repudiandae, placeat dolore consequuntur ratione aspernatur qui veritatis a atque. Quasi delectus, numquam odit quisquam odio minus blanditiis rem fugiat voluptate quaerat quam magnam atque nam.</p>\n<h1>Heading 1</h1>\n<h2>Heading 2</h2>\n<h3>Heading 3</h3>\n<h4>Heading 4</h4>\n<h5>Heading 5</h5>\n<h6>Heading 6</h6>','This is an example of plain text Lorem ipsum dolor, sit',NULL,NULL,NULL,NULL,1,'2018-12-22 14:13:56',1,'2019-01-11 14:46:03'),
  (15,2,'galleryGuide','gallery',1,'Gallery Style Guide','A gallery of images.','<p>A gallery of images.</p>','A gallery of images.',NULL,NULL,NULL,NULL,1,'2018-12-22 14:19:28',1,'2019-01-11 14:46:03'),
  (16,2,'collectionGuide','collection',1,'Collection Element','This is an element that requires a collection to be created and then selected from the dropdown below. This is for when you have data that needs summary/detail.','<p>This is an element that requires a collection to be created and then selected from the dropdown below. This is for when you have data that needs summary/detail.</p>','This is an element that requires a collection to be created',NULL,NULL,NULL,NULL,1,'2018-12-22 22:21:27',1,'2019-01-11 14:46:03'),
  (17,2,'imageGuide','image',1,'Image Element','For when you need to add an image element into a website. The image loading below is 600 x 400 px','<p>For when you need to add an image element into a website. The image loading below is 600 x 400 px</p>','For when you need to add an image element into a website.',NULL,NULL,'https://unsplash.it/1000/400',NULL,1,'2018-12-22 22:24:44',1,'2019-01-11 14:46:03'),
  (18,2,'videoGuide','video',1,'Video Element - Vimeo','For when you want to embed an external link that comes with its own embed code like an iframe that is used by both YouTube and Vimeo.\r\n\r\nThis is a Vimeo link using responsive settings from Vimeo.','<p>For when you want to embed an external link that comes with its own embed code like an iframe that is used by both YouTube and Vimeo.</p>\n<p>This is a Vimeo link using responsive settings from Vimeo.</p>','For when you want to embed an external link that comes with',NULL,NULL,NULL,'<iframe src=\"https://player.vimeo.com/video/259291697\" width=\"640\" height=\"360\" frameborder=\"0\" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>\r\n<p><a href=\"https://vimeo.com/259291697\">Travel Oregon - Only Slightly Exaggerated</a> from <a href=\"https://vimeo.com/suncreature\">Sun Creature Studio</a> on <a href=\"https://vimeo.com\">Vimeo</a>.</p>',1,'2018-12-22 22:27:38',1,'2019-01-11 14:46:03'),
  (19,2,'videoGuide','video',1,'Video Element - Youtube','This is a YouTube embed link.','<p>This is a YouTube embed link.</p>','This is a YouTube embed link.',NULL,NULL,NULL,'<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/Fllx5pzvfFQ\" frameborder=\"0\" allow=\"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>',1,'2018-12-22 22:33:55',1,'2019-01-11 14:46:03'),
  (22,4,'typoGuide','text',1,'Test Page','Cotton candy carrot cake muffin jujubes wafer oat cake pastry tootsie roll brownie. Biscuit marzipan jelly bonbon chocolate bar. Dragée cupcake sugar plum ice cream caramels sugar plum gingerbread tiramisu. Oat cake toffee jelly-o marshmallow donut. Macaroon dessert sesame snaps jujubes jelly cookie. Soufflé chocolate bar cookie cookie sweet roll sweet roll cake gummi bears. Soufflé toffee ice cream marzipan jelly beans. Icing soufflé bear claw apple pie cake marshmallow candy icing. Pudding lollipop pie dragée jelly beans marzipan. Tootsie roll pastry jujubes jujubes oat cake liquorice cake chocolate bar icing. Sugar plum marshmallow sweet roll cotton candy. Gummi bears chocolate bar tiramisu biscuit tart biscuit bonbon carrot cake apple pie.\r\n\r\nCroissant tootsie roll bear claw chocolate bar. Candy canes wafer ice cream. Tiramisu chocolate chocolate. Gummies sweet roll toffee. Caramels donut halvah tootsie roll cookie jelly marzipan cheesecake. Marzipan dessert tart chocolate bar candy canes cupcake chocolate muffin soufflé. Cotton candy chocolate cake jujubes cake bear claw ice cream sweet gingerbread lemon drops. Lollipop apple pie cake sweet. Gummi bears topping lollipop liquorice cake soufflé sweet roll donut. Oat cake powder danish. Biscuit sesame snaps muffin chocolate biscuit. Sweet roll liquorice cookie tart ice cream pastry cheesecake tart chocolate bar. Gingerbread caramels sesame snaps cupcake cake lollipop powder chocolate cake jelly-o. Oat cake marzipan cotton candy wafer liquorice.','<p>Cotton candy carrot cake muffin jujubes wafer oat cake pastry tootsie roll brownie. Biscuit marzipan jelly bonbon chocolate bar. Dragée cupcake sugar plum ice cream caramels sugar plum gingerbread tiramisu. Oat cake toffee jelly-o marshmallow donut. Macaroon dessert sesame snaps jujubes jelly cookie. Soufflé chocolate bar cookie cookie sweet roll sweet roll cake gummi bears. Soufflé toffee ice cream marzipan jelly beans. Icing soufflé bear claw apple pie cake marshmallow candy icing. Pudding lollipop pie dragée jelly beans marzipan. Tootsie roll pastry jujubes jujubes oat cake liquorice cake chocolate bar icing. Sugar plum marshmallow sweet roll cotton candy. Gummi bears chocolate bar tiramisu biscuit tart biscuit bonbon carrot cake apple pie.</p>\n<p>Croissant tootsie roll bear claw chocolate bar. Candy canes wafer ice cream. Tiramisu chocolate chocolate. Gummies sweet roll toffee. Caramels donut halvah tootsie roll cookie jelly marzipan cheesecake. Marzipan dessert tart chocolate bar candy canes cupcake chocolate muffin soufflé. Cotton candy chocolate cake jujubes cake bear claw ice cream sweet gingerbread lemon drops. Lollipop apple pie cake sweet. Gummi bears topping lollipop liquorice cake soufflé sweet roll donut. Oat cake powder danish. Biscuit sesame snaps muffin chocolate biscuit. Sweet roll liquorice cookie tart ice cream pastry cheesecake tart chocolate bar. Gingerbread caramels sesame snaps cupcake cake lollipop powder chocolate cake jelly-o. Oat cake marzipan cotton candy wafer liquorice.</p>','Cotton candy carrot cake muffin jujubes wafer oat cake',NULL,NULL,NULL,NULL,1,'2018-12-29 11:21:24',1,'2018-12-29 11:22:19'),
  (23,4,'heroGuide','hero',1,'Test Hero','This is a test page and test hero','<p>This is a test page and test hero</p>','This is a test page and test hero',NULL,NULL,'https://unsplash.it/1600/800',NULL,1,'2018-12-29 11:22:19',1,'2018-12-29 11:22:19'),
  (24,3,'galleryGuide','gallery',1,'Piton Gallery','This page represents the default gallery page. A gallery will need to be created before this page can be created. Currently it is populated with images from Unsplash.','<p>This page represents the default gallery page. A gallery will need to be created before this page can be created. Currently it is populated with images from Unsplash.</p>','This page represents the default gallery page. A gallery',NULL,NULL,NULL,NULL,1,'2018-12-29 14:12:38',1,'2019-01-11 14:45:49'),
  (25,4,'videoGuide','video',1,'Style Guide for Video','This is how the video will appear.','<p>This is how the video will appear.</p>','This is how the video will appear.',NULL,NULL,NULL,'<iframe width=\"560\" height=\"315\" src=\"https://www.youtube-nocookie.com/embed/Y7RAqOaj_JE\" frameborder=\"0\" allow=\"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>',1,'2019-01-11 14:27:16',1,'2019-01-11 14:53:08');

INSERT INTO `setting` (`category`, `sort_order`, `setting_key`, `setting_value`, `input_type`, `label`, `help`, `restricted`, `created_by`, `created_date`, `updated_by`, `updated_date`)
VALUES
  ('site',1,'theme','default','select','Theme',NULL,'N',1,now(),1,now()),
  ('site',2,'urlDomainName','example.com','','Domain Name','Do not include the http(s):// or a trailing slash. For use in generated sitemaps.','N',1,now(),1,now()),
  ('site',3,'urlScheme','http','select','URL Scheme','Select <code>http</code> or <code>https</code> (https requires additional server changes).','N',1,now(),1,now()),
  ('site',4,'dateFormat','mm/dd/yyyy','select','Date Format','Select date picker format to use across site.','N',1,now(),1,now()),
  ('site',5,'googleWebMaster','Unique GWM Key',NULL,'Google Webmaster Verification Link',NULL,'N',1,now(),1,now()),
  ('site',6,'googleAnalytics','Google Analytics Tracking Code',NULL,'Google Analytics Code',NULL,'N',1,now(),1,now()),
  ('site',7,'statCounter','Stat counter code',NULL,'Stat Counter',NULL,'N',1,now(),1,now()),
  ('contact',1,'displayName','Moritz Media',NULL,'Display Name',NULL,'N',1,now(),1,now()),
  ('contact',2,'telephone','555-1212',NULL,'Telephone',NULL,'N',1,now(),1,now()),
  ('contact',3,'mobile','541-555-1212',NULL,'Mobile',NULL,'N',1,now(),1,now()),
  ('contact',4,'address1','Building 4',NULL,'Address Line 1',NULL,'N',1,now(),1,now()),
  ('contact',5,'address2','1234 Main St',NULL,'Address Line 2',NULL,'N',1,now(),1,now()),
  ('contact',6,'address3','Flat 13',NULL,'Address Line 3',NULL,'N',1,now(),1,now()),
  ('contact',7,'city','Bend',NULL,'City',NULL,'N',1,now(),1,now()),
  ('contact',8,'province','OR',NULL,'State',NULL,'N',1,now(),1,now()),
  ('contact',9,'postalCode','TW1 2HU',NULL,'Postal Code',NULL,'N',1,now(),1,now()),
  ('contact',10,'country','United States',NULL,'Country',NULL,'N',1,now(),1,now()),
  ('social',1,'facebookLink','https://www.facebook.com/',NULL,'Facebook Link',NULL,'N',1,now(),1,now()),
  ('social',2,'twitterLink','https://twitter.com',NULL,'Twitter Link',NULL,'N',1,now(),1,now()),
  ('social',3,'instagramLink','https://www.instagram.com/',NULL,'Instagram Link',NULL,'N',1,now(),1,now()),
  ('social',4,'linkedinLink','https://www.linkedin.com/',NULL,'LinkedIn Link',NULL,'N',1,now(),1,now()),
  ('social',5,'githubLink','https://www.github.com/',NULL,'GitHub Link',NULL,'N',1,now(),1,now());

