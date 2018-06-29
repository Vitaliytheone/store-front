USE `stores`;

ALTER TABLE `stores`
  ADD INDEX `idx_status_created_at` (`status`,`created_at`);

