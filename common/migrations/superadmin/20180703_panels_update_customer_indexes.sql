USE `panels`;

ALTER TABLE `customers`
  ADD INDEX `idx_status` (`status`);