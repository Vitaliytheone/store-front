USE `panels`;
ALTER TABLE `ssl_validation` ADD `ptype` VARCHAR(1)  NULL  DEFAULT NULL  COMMENT '1-Panel, 2-Sommerce'  AFTER `id`;
ALTER TABLE `ssl_validation` DROP FOREIGN KEY `fk_ssl_validation__project`;

UPDATE `ssl_validation` SET `ptype` = '1';
