#
# TODO:: Only for new SOMMERCE branch!
#

USE `sommerce_template`;

ALTER TABLE `pages` ADD `name` VARCHAR(300) NOT NULL AFTER `id`;
ALTER TABLE `pages` CHANGE `title` `seo_title` varchar(300) NULL;