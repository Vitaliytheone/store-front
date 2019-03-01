USE `store`;

ALTER TABLE `orders` ADD `in_progress` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0 - disabled, 1 - enabled' AFTER `customer`;