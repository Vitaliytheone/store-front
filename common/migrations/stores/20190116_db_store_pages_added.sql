#USE `store`;

CREATE TABLE `pages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(300) DEFAULT NULL,
  `title` varchar(300) DEFAULT NULL,
  `visibility` tinyint(1) DEFAULT '0',
  `twig` text COMMENT 'editor twig source',
  `styles` text COMMENT 'editor styles source',
  `json` text COMMENT 'editor published json',
  `json_dev` text COMMENT 'editor unpublished json',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
