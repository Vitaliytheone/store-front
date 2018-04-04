USE `panels`;
ALTER TABLE `project` ADD `name_modal` TINYINT(1) NOT NULL DEFAULT '0' AFTER `name_fields`;
