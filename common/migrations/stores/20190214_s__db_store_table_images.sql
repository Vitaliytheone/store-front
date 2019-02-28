#
# TODO:: Only for new SOMMERCE branch!
#

CREATE TABLE `images` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `file_name` varchar(100) DEFAULT NULL,
  `file` longblob COMMENT 'File content',
  `cdn_id` varchar(100) DEFAULT NULL,
  `cdn_data` varchar(1000) DEFAULT NULL,
  `url` varchar(300) DEFAULT NULL,
  `thumbnail_url` varchar(300) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

