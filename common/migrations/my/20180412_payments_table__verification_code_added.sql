USE `panels`;
ALTER TABLE `payments` ADD `verification_code` VARCHAR(64)  NULL  DEFAULT NULL  AFTER `options`;

