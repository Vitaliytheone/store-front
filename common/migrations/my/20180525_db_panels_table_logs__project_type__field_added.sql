USE `panels`;
ALTER TABLE `logs` ADD `project_type` TINYINT(1)  NULL  DEFAULT NULL  COMMENT '1-Panel, 2-Store'  AFTER `id`;
UPDATE `logs` SET `project_type` = '1' WHERE `project_type` IS NULL;
ALTER TABLE `logs` CHANGE `panel_id` `panel_id` INT(11)  NOT NULL  COMMENT 'panel_id, store_id';
