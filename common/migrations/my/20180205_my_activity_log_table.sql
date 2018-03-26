CREATE TABLE `my_activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `super_user` tinyint(1) NOT NULL COMMENT '0 - customer, 1 - super user',
  `created_at` int(11) NOT NULL,
  `ip` varchar(300) NOT NULL,
  `controller` varchar(300) NOT NULL,
  `action` varchar(300) NOT NULL,
  `request_data` text NOT NULL,
  `details` varchar(1000) NOT NULL,
  `details_id` varchar(1000) NOT NULL,
  `event` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;