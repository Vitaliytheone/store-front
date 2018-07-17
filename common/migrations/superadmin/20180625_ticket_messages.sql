ALTER TABLE `ticket_messages` CHANGE COLUMN `cid` `customer_id` int(11) NOT NULL;
ALTER TABLE `ticket_messages` CHANGE COLUMN `uid` `admin_id` int(11) NOT NULL;
ALTER TABLE `ticket_messages` CHANGE COLUMN `tid` `ticket_id` int(11) NOT NULL;
ALTER TABLE `ticket_messages` CHANGE COLUMN `date` `created_at` int(11)  NOT NULL;

ALTER TABLE `ticket_messages`
  ADD `user_agent` VARCHAR(300) AFTER `admin_id`;


ALTER TABLE `ticket_messages`
  ADD `is_system` tinyint(1) DEFAULT 0 AFTER `user_agent`;



