USE `panels`;
ALTER TABLE `ssl_cert_item` ADD `generator` TINYINT(1)  NULL  DEFAULT NULL  AFTER `allow`;

UPDATE `ssl_cert_item` SET `generator` = '1' WHERE `product_id` = '45';
UPDATE `ssl_cert_item` SET `generator` = '2' WHERE `product_id` = '31';
