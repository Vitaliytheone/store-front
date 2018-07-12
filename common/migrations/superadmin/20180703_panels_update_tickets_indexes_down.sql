USE `panels`;

ALTER TABLE `tickets`
  DROP INDEX `idx_status`;

ALTER TABLE `tickets`
  DROP INDEX `idx_status_assigned_admin_id`;

ALTER TABLE `tickets`
  DROP INDEX `idx_assigned_admin_id`;