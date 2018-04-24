USE `panels`;

ALTER TABLE `ssl_cert` CHANGE `ptype` `project_type` TINYINT(1)  NULL  DEFAULT NULL  COMMENT '1 - Panel, 2 - Store';

ALTER TABLE `ssl_validation` DROP `ptype`;