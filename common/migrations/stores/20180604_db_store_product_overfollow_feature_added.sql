ALTER TABLE `suborders` ADD `overflow_quantity` INT(11)  NULL  DEFAULT '0'  AFTER `quantity`;
ALTER TABLE `packages` ADD `overflow` SMALLINT(3)  NULL  DEFAULT '0'  COMMENT '%, -100..100'  AFTER `quantity`;
