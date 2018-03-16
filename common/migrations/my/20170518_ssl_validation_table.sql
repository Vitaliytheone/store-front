DROP TABLE IF EXISTS `ssl_validation`;
CREATE TABLE `ssl_validation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `file_name` varchar(250) NOT NULL,
  `content` varchar(1000) NOT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  CONSTRAINT `fk_ssl_validation__project` FOREIGN KEY (`pid`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;