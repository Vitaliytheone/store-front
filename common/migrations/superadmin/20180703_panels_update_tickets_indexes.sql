USE `panels`;

ALTER TABLE `tickets`
  ADD INDEX `idx_status` (`status`);

ALTER TABLE `tickets`
  ADD INDEX `idx_status_assigned_admin_id` (`status`, `assigned_admin_id`);

ALTER TABLE `tickets`
  ADD INDEX `idx_assigned_admin_id` (`assigned_admin_id`);