USE `panels`;
ALTER TABLE `ssl_cert` ADD `ptype` TINYINT(1) NULL COMMENT '1 - Panel, 2 - Store' AFTER `cid`;

UPDATE `ssl_cert` SET `ptype` = '1';


