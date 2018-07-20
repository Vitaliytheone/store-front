-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE `stores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `subdomain` smallint(1) DEFAULT '0',
  `ssl` varchar(1) DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `timezone` int(11) DEFAULT NULL,
  `language` varchar(10) DEFAULT NULL,
  `status` tinyint(1) NOT NULL COMMENT '1 - active, 2 - frozen, 3 - terminated',
  `hide` tinyint(1) DEFAULT '0',
  `db_name` varchar(255) DEFAULT NULL,
  `trial` tinyint(1) NOT NULL DEFAULT '0',
  `expired` int(11) unsigned NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_keywords` varchar(2000) DEFAULT NULL,
  `seo_description` varchar(2000) DEFAULT NULL,
  `folder` varchar(255) DEFAULT NULL,
  `folder_content` varchar(255) DEFAULT NULL,
  `theme_name` varchar(255) DEFAULT NULL,
  `theme_folder` varchar(255) DEFAULT NULL,
  `block_slider` tinyint(1) NOT NULL DEFAULT '0',
  `block_features` tinyint(1) NOT NULL DEFAULT '0',
  `block_reviews` tinyint(1) NOT NULL DEFAULT '0',
  `block_process` tinyint(1) NOT NULL DEFAULT '0',
  `admin_email` varchar(300) DEFAULT NULL,
  `custom_header` text,
  `custom_footer` text,
  PRIMARY KEY (`id`),
  KEY `fk_customer_id_stores_idx` (`customer_id`),
  KEY `status` (`status`),
  KEY `idx_status_created_at` (`status`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2018-07-04 09:13:04
