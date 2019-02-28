USE `stores`;

ALTER TABLE `payment_gateways`
ADD `visibility` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '0 - hide, 1 - visible' AFTER `options`;