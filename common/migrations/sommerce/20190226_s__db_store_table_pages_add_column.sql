#
# TODO:: Only for new SOMMERCE branch!
#

ALTER TABLE `pages` ADD `seo_description` VARCHAR(2000) NULL DEFAULT NULL AFTER `title`;
ALTER TABLE `pages` ADD `seo_keywords` VARCHAR(2000) NULL DEFAULT NULL AFTER `seo_description`;