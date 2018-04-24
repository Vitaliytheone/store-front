
# USE 'store_template';
CREATE TABLE `languages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(5) NOT NULL DEFAULT '' COMMENT 'Language code in IETF lang format',
  `content` text COMMENT 'Json messages content',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_lang_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;