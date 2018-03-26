DROP TABLE IF EXISTS `super_admin`;
CREATE TABLE `super_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `created_at` int(11) NOT NULL,
  `first_name` varchar(250) NOT NULL,
  `last_name` varchar(250) NOT NULL,
  `last_login` varchar(250) DEFAULT NULL,
  `last_ip` varchar(250) DEFAULT NULL,
  `auth_key` varchar(250) NOT NULL,
  `rules` varchar(1000) DEFAULT NULL,
  `status` tinyint(4) NOT NULL COMMENT '0 – suspended; 1 - active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;