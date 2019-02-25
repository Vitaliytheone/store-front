USE `panels`;

ALTER TABLE `domains` ADD `registrar` VARCHAR(250) NOT NULL AFTER `details`;
UPDATE `domains` SET `registrar`='ahnames';
ALTER TABLE `domain_zones` ADD `registrar` VARCHAR(250) NOT NULL AFTER `price_transfer`;
UPDATE `domain_zones` SET `registrar`='ahnames';
INSERT INTO `params` (`id`, `category`, `code`, `options`, `updated_at`, `position`) VALUES (NULL, 'service', 'namesilo', '{\"namesilo.url\":\"https://www.namesilo.com/api\",\"namesilo.key\":\"6f3bb35a23962b15be15c3c\",\"namesilo.payment_id\":\"485\",\"namesilo.version\":\"1\",\"namesilo.type\":\"xml\",\"namesilo.testmode\":\"0\",\"namesilo.contact_id\":\"\"}', '1548833103', NULL);
INSERT INTO `domain_zones` (`id`, `zone`, `price_register`, `price_renewal`, `price_transfer`, `registrar`) VALUES (NULL, '.XYZ', '2.00', '2.00', '2.00', 'namesilo');