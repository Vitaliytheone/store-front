DROP TABLE IF EXISTS `super_log`;
CREATE TABLE `super_log` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(250) NOT NULL,
  `params` varchar(1000) NOT NULL,
  `ip` varchar(250) NOT NULL,
  `user_agent` varchar(1000) NOT NULL,
  `created_at` int(11) NOT NULL,
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `fk_super_log__super_admin` FOREIGN KEY (`admin_id`) REFERENCES `super_admin` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;