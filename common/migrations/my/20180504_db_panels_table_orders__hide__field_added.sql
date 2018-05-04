USE `panels`;
ALTER TABLE `orders` ADD `hide` TINYINT(1)  NULL  DEFAULT '0'  AFTER `status`;
