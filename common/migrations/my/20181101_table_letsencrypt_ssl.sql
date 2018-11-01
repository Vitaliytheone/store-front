CREATE TABLE `letsencrypt_ssl` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(300) DEFAULT NULL,
  `file_contents` text,
  `expired_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `letsencrypt_ssl` ADD UNIQUE INDEX (`domain`);
