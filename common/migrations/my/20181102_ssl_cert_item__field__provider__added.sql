ALTER TABLE `ssl_cert_item` ADD `provider` TINYINT(1)  NULL  DEFAULT NULL  COMMENT '1 - gogetssl, 2 - letsencrypt'  AFTER `generator`;
UPDATE `ssl_cert_item` SET `provider` = '1';