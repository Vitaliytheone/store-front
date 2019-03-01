USE `stores`;
ALTER TABLE `stores` ADD `hide` TINYINT(1)  NULL  DEFAULT '0'  AFTER `status`;
