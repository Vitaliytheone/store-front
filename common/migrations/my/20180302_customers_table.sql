USE `panels`;
ALTER TABLE `customers` ADD `stores` TINYINT(1)  NOT NULL  DEFAULT '0'  AFTER `child_panels`;
