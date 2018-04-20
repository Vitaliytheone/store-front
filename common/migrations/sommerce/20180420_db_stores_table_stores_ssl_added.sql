# USE `stores`;
ALTER TABLE `stores` ADD `ssl` VARCHAR(1)  NULL  DEFAULT '0'  AFTER `subdomain`;
