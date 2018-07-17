use `panels`;

ALTER TABLE `tickets` CHANGE COLUMN `cid` `customer_id` int(11) NOT NULL;
ALTER TABLE `tickets` CHANGE COLUMN `admin_id` `admin_id` int(11) DEFAULT NULL;
ALTER TABLE `tickets` CHANGE COLUMN `admin` `is_admin` tinyint(1)  NOT NULL;
ALTER TABLE `tickets` CHANGE COLUMN `user` `is_user` tinyint(1)  NOT NULL;
ALTER TABLE `tickets` CHANGE COLUMN `date` `created_at` int(11)  NOT NULL;
ALTER TABLE `tickets` CHANGE COLUMN `date_update` `updated_at` int(11)  NOT NULL;

ALTER TABLE `tickets`
  DROP COLUMN `pid`,
  DROP COLUMN `message`;

ALTER TABLE `tickets`
  ADD `user_agent` VARCHAR(300)  AFTER `is_user`;

ALTER TABLE `tickets`
  ADD `assigned_admin_id` int(11) DEFAULT NULL AFTER `admin_id`;



