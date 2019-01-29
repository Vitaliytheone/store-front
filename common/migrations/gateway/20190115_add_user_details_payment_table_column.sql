USE `gateway`;

ALTER TABLE `payments` ADD `user_details` text NULL AFTER `status`;
