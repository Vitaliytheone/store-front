DROP TABLE IF EXISTS `invoice_details`;
CREATE TABLE `invoice_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `description` text,
  `amount` decimal(10,2) NOT NULL,
  `item` tinyint(2) NOT NULL COMMENT '1 – buy panel, 2 – prolongation panel, 3 – buy domain, 4 – prolongation domain, 5 – buy ssl certification, 6 – prolongation ssl certification',
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  CONSTRAINT `fk_invoice_details__invoices` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;