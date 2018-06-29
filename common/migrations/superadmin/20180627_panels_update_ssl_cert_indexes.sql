USE `panels`;

ALTER TABLE `ssl_cert`
  ADD INDEX `idx_status_created_at` (`status`,`created_at`);

