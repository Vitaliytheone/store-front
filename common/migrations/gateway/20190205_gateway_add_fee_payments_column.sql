USE `gateway`;

ALTER TABLE `payments` ADD `take_fee_from_user` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - not active; 1 - active' AFTER `transaction_id`;
ALTER TABLE `payments` ADD `fee` decimal(20,5) NULL AFTER `take_fee_from_user`;
