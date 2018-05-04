USE `panels`;
ALTER TABLE `project` ADD `hide` TINYINT(1)  NULL  DEFAULT '0'  AFTER `act`;
