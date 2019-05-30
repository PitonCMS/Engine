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
  `collection_slug` varchar(100) NULL DEFAULT NULL,
  `page_slug` varchar(100) NOT NULL,
  `definition` varchar(60) NULL DEFAULT NULL,
  `template` varchar(60) NOT NULL,
  `title` varchar(60) NULL DEFAULT NULL,
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

CREATE TABLE IF NOT EXISTS `page_setting` (
  `id` int NOT NULL AUTO_INCREMENT,
  `page_id` int NOT NULL,
  `setting_key` varchar(60) NOT NULL,
  `setting_value` varchar(1000) DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 1,
  `created_date` datetime NOT NULL,
  `updated_by` int(11) NOT NULL DEFAULT 1,
  `updated_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_setting_uq` (`page_id`,`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `setting` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category` varchar(60) NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `sort` smallint NOT NULL DEFAULT 1,
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
  (1,NULL,'home','home.json','home.html','Home',NULL,'All about this page for SEO.','2018-12-27',NULL,1,now(),1,now()),
  (2,NULL,'styles','style.json','style.html','Styles',NULL,'Style Guide for PitonCMS','2018-12-27',NULL,1,now(),1,now());

INSERT INTO `page_element` (`id`, `page_id`, `block_key`, `definition`, `template`, `element_sort`, `title`, `content_raw`, `content`, `excerpt`, `collection_slug`, `gallery_id`, `image_path`, `embedded`, `created_by`, `created_date`, `updated_by`, `updated_date`)
VALUES
  (1,1,'aboveTheFoldHero','hero.json','hero.html',1,'Welcome to PitonCMS','A flexible content management system for your personal website.','<p>A flexible content management system for your personal website.</p>','A flexible content management system for your personal',NULL,NULL,NULL,NULL,1,now(),1,now()),
  (2,1,'introBlock','text.json','text.html',1,'Where to Start?','Congratulations! You have successfully installed PitonCMS. \r\n\r\nTo start, you will want to read the documentation on how to setup and configure your new site <a href=\"https://github.com/pitoncms\" target=\"_blank\">here</a>. Follow the easy step-by-step process for creating your own personalized theme.  \r\n\r\n','<p>Congratulations! You have successfully installed PitonCMS. </p>\n<p>To start, you will want to read the documentation on how to setup and configure your new site <a href=\"https://github.com/pitoncms\" target=\"_blank\">here</a>. Follow the easy step-by-step process for creating your own personalized theme.  </p>','Congratulations! You have successfully installed PitonCMS.',NULL,NULL,NULL,NULL,1,now(),1,now()),
  (3,2,'typoGuide','text.json','text.html',1,'PitonCMS Typography Style Guide','<p>This is an example of plain text</p>\r\n<p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Reiciendis sit provident quas quia deserunt, officiis repellendus vel praesentium esse eius iure perferendis sunt id fugiat quis nemo nobis incidunt? Ad temporibus sit impedit! Repudiandae, placeat dolore consequuntur ratione aspernatur qui veritatis a atque. Quasi delectus, numquam odit quisquam odio minus blanditiis rem fugiat voluptate quaerat quam magnam atque nam.</p>\r\n\r\n<h1>Heading 1</h1>\r\n<h2>Heading 2</h2>\r\n<h3>Heading 3</h3>\r\n<h4>Heading 4</h4>\r\n<h5>Heading 5</h5>\r\n<h6>Heading 6</h6>\r\n\r\n<p>PitonCMS uses Mike Reithmuller\'s Precision Responsive Typography to create responsive text. You will find the sass file in <code>/structure/sass/var/_typography.scss</code>. Here is a <a href=\"https://codepen.io/MadeByMike/pen/YPJJYv\" target=\"_blank\">link</a> <i class=\"fas fa-external-link-alt\"></i> to the original CodePen.','<p>This is an example of plain text</p>\n<p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Reiciendis sit provident quas quia deserunt, officiis repellendus vel praesentium esse eius iure perferendis sunt id fugiat quis nemo nobis incidunt? Ad temporibus sit impedit! Repudiandae, placeat dolore consequuntur ratione aspernatur qui veritatis a atque. Quasi delectus, numquam odit quisquam odio minus blanditiis rem fugiat voluptate quaerat quam magnam atque nam.</p>\n<h1>Heading 1</h1>\n<h2>Heading 2</h2>\n<h3>Heading 3</h3>\n<h4>Heading 4</h4>\n<h5>Heading 5</h5>\n<h6>Heading 6</h6>\n<p>PitonCMS uses Mike Reithmuller\'s Precision Responsive Typography to create responsive text. You will find the sass file in <code>/structure/sass/var/_typography.scss</code>. Here is a <a href=\"https://codepen.io/MadeByMike/pen/YPJJYv\" target=\"_blank\">link</a> <i class=\"fas fa-external-link-alt\"></i> to the original CodePen.','This is an example of plain text Lorem ipsum dolor, sit',NULL,NULL,NULL,NULL,1,now(),1,now()),
  (4,2,'galleryGuide','gallery.json','gallery.html',1,'Gallery Style Guide','A gallery of images.','<p>A gallery of images.</p>','A gallery of images.',NULL,NULL,NULL,NULL,1,now(),1,now()),
  (5,2,'videoGuide','video.json','video.html',1,'Video Element','For when you want to embed an external link that comes with its own embed code like an iframe that is used by both YouTube and Vimeo.\r\n\r\nThis is a Vimeo link using responsive settings from Vimeo.','<p>For when you want to embed an external link that comes with its own embed code like an iframe that is used by both YouTube and Vimeo.</p>\n<p>This is a Vimeo link using responsive settings from Vimeo.</p>','For when you want to embed an external link that comes with',NULL,NULL,NULL,'<iframe src=\"https://player.vimeo.com/video/259291697\" width=\"640\" height=\"360\" frameborder=\"0\" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>\r\n<p><a href=\"https://vimeo.com/259291697\">Travel Oregon - Only Slightly Exaggerated</a> from <a href=\"https://vimeo.com/suncreature\">Sun Creature Studio</a> on <a href=\"https://vimeo.com\">Vimeo</a>.</p>',1,now(),1,now()),
  (6,2,'typoGuide','text.json','text.html',2,'Page Settings','You can add individual pieces of data that are specific to a page. For example if you wanted to create a recipe blog and wanted to add fields for servings, oven temp, etc., you could use the following code in your recipe.json file.<br>\r\n<pre>  \"settings\": [\r\n        {\r\n            \"label\": \"Servings\",\r\n            \"key\": \"servings\",\r\n            \"value\": \"\",\r\n            \"inputType\": \"input\",\r\n            \"help\": \"Number of servings.\"\r\n        },\r\n        {\r\n            \"label\": \"Oven Temperature\",\r\n            \"key\": \"ovenTemp\",\r\n            \"value\": \"\",\r\n            \"inputType\": \"input\",\r\n            \"help\": \"In Farenheit.\"\r\n        },\r\n        {\r\n            \"label\": \"Prep Time\",\r\n            \"key\": \"prepTime\",\r\n            \"value\": \"\",\r\n            \"inputType\": \"input\",\r\n            \"help\": \"In hours:minutes.\"\r\n        },\r\n        {\r\n            \"label\": \"Cook Time\",\r\n            \"key\": \"cookTime\",\r\n            \"value\": \"\",\r\n            \"inputType\": \"input\",\r\n            \"help\": \"In hours:minutes.\"\r\n        },\r\n        {\r\n            \"label\": \"Categories\",\r\n            \"key\": \"categories\",\r\n            \"value\": \"Entree\",\r\n            \"inputType\": \"select\",\r\n            \"help\": \"Select the category.\",\r\n            \"options\": [\r\n                {\r\n                    \"value\": \"entree\",\r\n                    \"name\": \"Entree\"\r\n                },\r\n                {\r\n                    \"value\": \"meat\",\r\n                    \"name\": \"Meat\"\r\n                },\r\n                {\r\n                    \"value\": \"vegetable\",\r\n                    \"name\": \"Vegetarian\"\r\n                }\r\n            ]\r\n        }\r\n    ]\r\n		</pre>','<p>You can add individual pieces of data that are specific to a page. For example if you wanted to create a recipe blog and wanted to add fields for servings, oven temp, etc., you could use the following code in your recipe.json file.<br></p>\n<pre>  \"settings\": [\n        {\n            \"label\": \"Servings\",\n            \"key\": \"servings\",\n            \"value\": \"\",\n            \"inputType\": \"input\",\n            \"help\": \"Number of servings.\"\n        },\n        {\n            \"label\": \"Oven Temperature\",\n            \"key\": \"ovenTemp\",\n            \"value\": \"\",\n            \"inputType\": \"input\",\n            \"help\": \"In Farenheit.\"\n        },\n        {\n            \"label\": \"Prep Time\",\n            \"key\": \"prepTime\",\n            \"value\": \"\",\n            \"inputType\": \"input\",\n            \"help\": \"In hours:minutes.\"\n        },\n        {\n            \"label\": \"Cook Time\",\n            \"key\": \"cookTime\",\n            \"value\": \"\",\n            \"inputType\": \"input\",\n            \"help\": \"In hours:minutes.\"\n        },\n        {\n            \"label\": \"Categories\",\n            \"key\": \"categories\",\n            \"value\": \"Entree\",\n            \"inputType\": \"select\",\n            \"help\": \"Select the category.\",\n            \"options\": [\n                {\n                    \"value\": \"entree\",\n                    \"name\": \"Entree\"\n                },\n                {\n                    \"value\": \"meat\",\n                    \"name\": \"Meat\"\n                },\n                {\n                    \"value\": \"vegetable\",\n                    \"name\": \"Vegetarian\"\n                }\n            ]\n        }\n    ]\n        </pre>','You can add individual pieces of data that are specific to',NULL,NULL,NULL,NULL,1,now(),1,now());

INSERT INTO `page_setting` (`page_id`, `setting_key`, `setting_value`, `created_by`, `created_date`, `updated_by`, `updated_date`)
VALUES
  (1,'ctaTitle','Read more on Github',1,now(),1,now()),
  (1,'ctaTarget','https://github.com/pitoncms',1,now(),1,now());
