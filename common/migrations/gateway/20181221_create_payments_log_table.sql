USE `gateway`;

DROP TABLE IF EXISTS `payments_log`;
CREATE TABLE `payments_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_id` int(11) NOT NULL,
  `response` text NOT NULL,
  `ip` varchar(300) DEFAULT NULL,
  `user_agent` varchar(300) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_id` (`payment_id`),
  CONSTRAINT `fk_payments_log__payments` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`)
) ENGINE=InnoDB;