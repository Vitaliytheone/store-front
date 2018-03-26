ALTER TABLE `super_tasks` ADD `comment` VARCHAR(3000)  NULL  DEFAULT NULL  AFTER `status`;
ALTER TABLE `super_tasks` ADD `item_id` INT(11)  NULL  DEFAULT NULL  AFTER `status`;
ALTER TABLE `super_tasks` CHANGE `created_at` `created_at` INT(11)  NULL  DEFAULT NULL;
ALTER TABLE `super_tasks` CHANGE `done_at` `done_at` INT(11)  NULL  DEFAULT NULL;