USE `panels`;
CREATE TABLE `my_verified_paypal` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` int(11) unsigned NOT NULL,
  `paypal_payer_id` varchar(100) DEFAULT NULL COMMENT 'Payer id from GetTransactionDetails.PAYERID',
  `paypal_payer_email` varchar(300) DEFAULT NULL COMMENT 'Payer email from GetTransactionDetails.EMAIL',
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `updated_at` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;