/* Panel type column added. Updated all records to panel type=1 */

ALTER TABLE `super_tools_levopanel` ADD `panel` TINYINT(2)  UNSIGNED  NULL  DEFAULT NULL  AFTER `id`;
ALTER TABLE `super_tools_levopanel` CHANGE `panel` `panel` TINYINT(2)  UNSIGNED  NULL  DEFAULT NULL  COMMENT '1-Levopanel, 2-Smmfire';
UPDATE `super_tools_levopanel` SET `panel`=1;