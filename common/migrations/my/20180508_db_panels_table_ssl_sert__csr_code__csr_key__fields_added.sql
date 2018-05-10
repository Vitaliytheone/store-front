USE `panels`;
ALTER TABLE `ssl_cert` ADD `csr_code` TEXT  NULL  AFTER `domain`;
ALTER TABLE `ssl_cert` ADD `csr_key` TEXT  NULL  AFTER `csr_code`;