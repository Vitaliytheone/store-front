ALTER TABLE `ssl_cert` ADD `expiry_at_timestamp` INT(11)  UNSIGNED  NULL  DEFAULT NULL  COMMENT 'Expiry date in timestamp format'  AFTER `expiry`;


