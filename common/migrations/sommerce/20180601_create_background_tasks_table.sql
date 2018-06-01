USE `panels`;

CREATE TABLE `background_tasks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(300) NOT NULL,
  `type` tinyint(1) NOT NULL COMMENT '1 - panels; 2 - stores',
  `code` varchar(300) NOT NULL,
  `data` text NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '0 - pending; 1 - in progress; 2 - completed; 3 - error',
  `response` text,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;