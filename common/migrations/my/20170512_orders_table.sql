ALTER TABLE `orders` ADD `item` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1 – buy panel, 2 – buy domain, 3 – buy ssl certification';
ALTER TABLE `orders` DROP `inputdomain`;
ALTER TABLE `orders` DROP `username`;
ALTER TABLE `orders` DROP `password`;