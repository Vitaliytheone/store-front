DROP TABLE IF EXISTS `ssl_cert`;
CREATE TABLE `ssl_cert` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `status` tinyint(2) NOT NULL COMMENT '0 - Pending; 4 - Cancel; 1 - Active; 2 - Processing; 3 - Processing(payment needed); 5 - Incomplete; 6 - Expiry',
  `details` text NOT NULL,
  `expiry` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `fk_ssl_cert__customers` FOREIGN KEY (`cid`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ssl_cert__ssl_cert_item` FOREIGN KEY (`item_id`) REFERENCES `ssl_cert_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;