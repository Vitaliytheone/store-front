USE `panels`;
ALTER TABLE `customers` ADD `buy_domain` TINYINT(1)  NULL  DEFAULT '0'  AFTER `stores`;

