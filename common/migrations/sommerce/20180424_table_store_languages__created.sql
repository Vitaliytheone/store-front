
USE 'store_template';

CREATE TABLE `languages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(5) DEFAULT NULL COMMENT 'Language code in IETF lang format',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_lang_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `messages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lang_code` varchar(10) DEFAULT NULL COMMENT 'Language code in IETF lang format',
  `section` varchar(100) DEFAULT NULL COMMENT 'Message section',
  `name` varchar(500) DEFAULT NULL COMMENT 'Message variable name',
  `value` varchar(2000) DEFAULT NULL COMMENT 'Message text',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `messages` ADD INDEX `idx_lang_code__name` (`lang_code`, `name`);


USE `stores`;

CREATE TABLE `store_default_messages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lang_code` varchar(10) DEFAULT NULL COMMENT 'Language code in IETF lang format',
  `section` varchar(100) DEFAULT NULL COMMENT 'Message section',
  `name` varchar(500) DEFAULT NULL COMMENT 'Message variable name',
  `value` varchar(2000) DEFAULT NULL COMMENT 'Message text',
  PRIMARY KEY (`id`),
  KEY `idx__lang_code_name` (`lang_code`,`name`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `store_default_messages` ADD INDEX `idx_lang_code__name` (`lang_code`, `name`);
