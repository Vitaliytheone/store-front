USE `panels`;

ALTER TABLE `ticket_messages` CHANGE COLUMN `customer_id` `cid` int(11) NOT NULL;
ALTER TABLE `ticket_messages` CHANGE COLUMN `admin_id` `uid` int(11) NOT NULL;
ALTER TABLE `ticket_messages` CHANGE COLUMN `ticket_id` `tid` int(11) NOT NULL;
ALTER TABLE `ticket_messages` CHANGE COLUMN `created_at` `date` int(11)  NOT NULL;

ALTER TABLE `ticket_messages`
  DROP COLUMN `user_agent`,
  DROP COLUMN `is_system`;




