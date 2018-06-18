USE `panels`;
ALTER TABLE `ssl_cert_item` ADD `allow` TEXT  NULL  COMMENT 'List of ids of allowed users for this cert. Allowed for all if NULL'  AFTER `price`;
