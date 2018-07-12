USE `panels`;

ALTER TABLE `tickets` CHANGE COLUMN `customer_id` `cid` int(11) NOT NULL;
ALTER TABLE `tickets` CHANGE COLUMN `admin_id` `admin_id` int(11) NOT NULL;
ALTER TABLE `tickets` CHANGE COLUMN `is_admin` `admin` tinyint(1)  NOT NULL;
ALTER TABLE `tickets` CHANGE COLUMN `is_user` `user` tinyint(1)  NOT NULL;
ALTER TABLE `tickets` CHANGE COLUMN `created_at` `date` int(11)  NOT NULL;
ALTER TABLE `tickets` CHANGE COLUMN `updated_at` `date_update` int(11)  NOT NULL;

ALTER TABLE `tickets`
  DROP COLUMN `user_agent`,
  DROP COLUMN `assigned_admin_id`;

ALTER TABLE `tickets`
  ADD `pid` int(11) NOT NULL AFTER `user`;

ALTER TABLE `tickets`
  ADD `message` varchar(1000) NOT NULL AFTER `pid`;




