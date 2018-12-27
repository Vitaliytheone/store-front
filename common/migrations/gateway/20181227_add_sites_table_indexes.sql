USE `gateways`;

ALTER TABLE `sites`
    CHANGE `customer_id` `customer_id` int(11) unsigned NOT NULL AFTER `id`;

ALTER TABLE `sites`
    ADD INDEX `idx_customer_id` (`customer_id`);