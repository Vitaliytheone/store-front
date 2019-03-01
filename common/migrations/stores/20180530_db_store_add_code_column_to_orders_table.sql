ALTER TABLE `orders` ADD `code` varchar(64) NOT NULL AFTER `id`;
ALTER TABLE `orders` ADD INDEX `idx_code` (`code`);