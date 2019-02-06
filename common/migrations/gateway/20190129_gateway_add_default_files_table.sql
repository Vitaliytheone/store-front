USE `gateway`;

ALTER TABLE `themes_files`
DROP `theme_id`,
ADD `url` varchar(300) NULL AFTER `name`,
ADD `file_type` varchar(300) NULL AFTER `url`,
ADD `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - not default; 1 - default' AFTER `content`,
ADD `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - not deleted; 1 - deleted' AFTER `is_default`,
RENAME TO `files`;
