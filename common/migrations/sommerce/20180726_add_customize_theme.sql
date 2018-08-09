USE 'STORES';

ALTER TABLE `default_themes` ADD `is_customize` tinyint(1) DEFAULT 0;
INSERT INTO `default_themes` (`name`, `folder`, `position`, `thumbnail`, `is_customize`) VALUES ('Classic', 'store_classic', '4', '', '1');
