#
# TODO:: Only for new SOMMERCE branch!
#

USE `sommerce_template`;

ALTER TABLE `suborders` CHANGE `amount` `amount` DECIMAL(20,2) NULL DEFAULT NULL;
ALTER TABLE `payments` CHANGE `amount` `amount` DECIMAL(20,2) NULL DEFAULT NULL;
ALTER TABLE `payments` CHANGE `fee` `fee` DECIMAL(20,2) NULL DEFAULT '0.00';