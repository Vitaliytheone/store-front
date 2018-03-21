ALTER TABLE `tickets` CHANGE `status` `status` int(11) NOT NULL COMMENT '0 - pending; 1 - respinded; 2 - closed; 3 - in progress; 4 - Solved;' AFTER `user`;

ALTER TABLE `tickets` CHANGE `ip` `ip` varchar(300) COLLATE 'utf8_general_ci' NULL AFTER `date_update`;