#
# TODO:: Only for new SOMMERCE branch!
#

ALTER TABLE `packages` ADD `icon` VARCHAR(180)  NULL  DEFAULT NULL  AFTER `position`;
ALTER TABLE `packages` ADD `properties` TEXT  NULL  AFTER `icon`;
