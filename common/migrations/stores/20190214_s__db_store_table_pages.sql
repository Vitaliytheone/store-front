#
# TODO:: Only for new SOMMERCE branch!
#

DROP TABLE `pages`;

CREATE TABLE `pages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(300) DEFAULT NULL,
  `title` varchar(300) DEFAULT NULL,
  `visibility` tinyint(1) DEFAULT '0',
  `twig` longtext COMMENT 'editor twig source',
  `json` longtext COMMENT 'editor published json',
  `json_draft` longtext COMMENT 'editor unpublished json',
  `is_draft` tinyint(1) DEFAULT '0' COMMENT 'is draft page',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `publish_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;