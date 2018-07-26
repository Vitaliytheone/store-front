USE 'STORES';

ALTER TABLE `default_themes` ADD `customize` tinyint(1) DEFAULT 0;
INSERT INTO `default_themes` (`name`, `folder`, `position`, `thumbnail`, `customize`) VALUES ('Classic', 'store_classic', '4', '/img/themes/preview_smm24.png', '1');
