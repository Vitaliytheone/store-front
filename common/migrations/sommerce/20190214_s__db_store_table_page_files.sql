#
# TODO:: Only for new SOMMERCE branch!
#

CREATE TABLE `page_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL COMMENT 'js, header, footer, styles',
  `content` longblob COMMENT 'binary or compilled content',
  `json` longtext COMMENT 'src content',
  `json_draft` longtext COMMENT 'src draft content',
  `file_type` varchar(10) DEFAULT NULL COMMENT 'js, css, twig',
  `is_draft` tinyint(1) DEFAULT '0' COMMENT 'is draft file',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `publish_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
