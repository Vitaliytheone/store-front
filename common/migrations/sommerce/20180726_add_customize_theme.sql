USE 'STORES';

ALTER TABLE `default_themes` ADD `customize_js` tinyint(1) DEFAULT 0;
INSERT INTO `default_themes` (`name`, `folder`, `position`, `thumbnail`, `customize_js`) VALUES ('Classic', 'store_classic', '4', '', '1');
