USE `panels`;
ALTER TABLE `getstatus` ADD `type` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0 - panels external, 1 - panels internal, 2 - stores external, 3 - stores internal' AFTER `status`;
ALTER TABLE `getstatus` ADD `updated_at` INT NOT NULL AFTER `type`;

