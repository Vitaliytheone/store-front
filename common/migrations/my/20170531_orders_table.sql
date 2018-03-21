ALTER TABLE `orders` ADD `domain` varchar(300) COLLATE 'utf8_general_ci' NULL AFTER `ip`;
ALTER TABLE `orders` ADD INDEX `domain` (`domain`);