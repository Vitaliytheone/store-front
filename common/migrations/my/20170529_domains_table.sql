DROP TABLE IF EXISTS `domains`;
CREATE TABLE `domains` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `zone_id` int(11) NOT NULL,
  `contact_id` varchar(250) NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '1 – ok, 2 - expired',
  `domain` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `created_at` int(11) NOT NULL,
  `expiry` int(11) NOT NULL,
  `privacy_protection` tinyint(1) NOT NULL,
  `transfer_protection` tinyint(1) NOT NULL,
  `details` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `zone_id` (`zone_id`),
  CONSTRAINT `fk_domains__customers` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_domains__domain_zones` FOREIGN KEY (`zone_id`) REFERENCES `domain_zones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;