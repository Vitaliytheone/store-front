ALTER TABLE `invoices` ADD `credit` DECIMAL(10,2) NOT NULL DEFAULT '0' AFTER `total`;

CREATE TABLE `super_credits_log` (
  `id` int(11) NOT NULL,
  `super_admin_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `credit` decimal(10,2) NOT NULL,
  `memo` varchar(300) NOT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `super_credits_log`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `super_credits_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;