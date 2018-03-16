CREATE TABLE `super_tools_levopanel` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) DEFAULT NULL,
  `server_ip` varchar(50) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `details` varchar(1000) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;