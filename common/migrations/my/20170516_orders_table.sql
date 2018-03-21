ALTER TABLE `orders` DROP `currency`;
ALTER TABLE `orders` DROP `domain`;
ALTER TABLE `orders` CHANGE `details` `details` text COLLATE 'utf8_general_ci' NOT NULL AFTER `ip`;
ALTER TABLE `orders` CHANGE `item` `item` tinyint(2) NOT NULL DEFAULT 1 COMMENT '1 - buy panel; 2 - buy domain; 3 - buy ssl' AFTER `details`;

