ALTER TABLE `notifications` RENAME TO `notification_email`;
ALTER TABLE `notification_email` ADD `enabled` tinyint(1) NOT NULL DEFAULT 1;
ALTER TABLE `notification_email` DROP `name`;
ALTER TABLE `notification_email` CHANGE `title` `subject` text COLLATE 'utf8_general_ci' NOT NULL AFTER `id`;
DELETE FROM `notification_email` WHERE ((`code` = 'ssl_action_needed'));