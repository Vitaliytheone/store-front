DROP TABLE IF EXISTS `third_party_log`;
CREATE TABLE `third_party_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item` tinyint(2) NOT NULL COMMENT '1 – buy panel, 2 – prolongation panel, 3 – buy domain, 4 – prolongation domain, 5 – buy ssl certification, 6 – prolongation ssl certification',
  `item_id` int(11) NOT NULL,
  `details` text,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;