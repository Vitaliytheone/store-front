USE `panels`;

ALTER TABLE `domains`
  ADD INDEX `idx_status_created_at` (`status`,`created_at`);
