USE `stores`;

ALTER TABLE `stores` ADD `last_count` int(11) DEFAULT '0';
ALTER TABLE `stores` ADD `current_count` int(11) DEFAULT '0';
