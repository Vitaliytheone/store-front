#
# TODO:: Only for new SOMMERCE branch!
#

ALTER TABLE `page_files` ADD `file_name` VARCHAR(200)  NULL  DEFAULT NULL  COMMENT 'filename'  AFTER `id`;

UPDATE `page_files` SET `file_name` = CONCAT(`name` , '.', `file_type`);

ALTER TABLE `page_files` CHANGE `name` `name_react` VARCHAR(32)  CHARACTER SET utf8mb4  COLLATE utf8mb4_general_ci  NULL  DEFAULT NULL  COMMENT 'js, header, footer, styles';
