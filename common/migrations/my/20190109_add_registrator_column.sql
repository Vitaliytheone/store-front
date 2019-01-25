USE `panels`;

ALTER TABLE `domains` ADD `registrar` VARCHAR(250) NOT NULL AFTER `details`;
UPDATE `domains` SET `registrar`='ahnames';
ALTER TABLE `domain_zones` ADD `registrar` VARCHAR(250) NOT NULL AFTER `price_transfer`;
UPDATE `domain_zones` SET `registrar`='ahnames';