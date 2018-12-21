USE `gateway`;

DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source_type` tinyint(1) NOT NULL COMMENT '1 - panel; 2- store',
  `source_id` int(11) NOT NULL,
  `source_payment_id` int(11) NOT NULL,
  `method_id` int(11) NOT NULL,
  `currency` char(3) NOT NULL,
  `amount` decimal(20,5) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - pending; 1 - completed; 2 - expired; 3 - writing; 4 - fail; 5 - hold',
  `response_status` varchar(300) DEFAULT NULL,
  `response` varchar(1000) DEFAULT NULL,
  `success_url` varchar(300) DEFAULT NULL,
  `fail_url` varchar(300) DEFAULT NULL,
  `return_url` varchar(300) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;