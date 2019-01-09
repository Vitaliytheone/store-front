USE `gateways`;

ALTER TABLE `payment_methods`
ADD `class_name` varchar(300) NOT NULL AFTER `method_name`,
ADD `url` varchar(300) NOT NULL AFTER `class_name`;