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


SET NAMES utf8mb4;

CREATE TABLE `store_admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(64) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL COMMENT '1 - active2 - suspended',
  `ip` varchar(255) DEFAULT NULL,
  `last_login` int(11) DEFAULT NULL,
  `rules` varchar(1000) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_admin_store_id_idx` (`store_id`),
  KEY `idx_id_store_id_status` (`id`,`store_id`,`status`),
  CONSTRAINT `fk_admin_store_id` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `store_admins_hash` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `hash` varchar(64) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `super_user` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - admin, 1 - superadmin',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `hash` (`hash`),
  KEY `__admin_id__hash` (`admin_id`,`hash`),
  KEY `idx_hash` (`hash`),
  KEY `idx_admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `store_domains` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) DEFAULT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `type` tinyint(1) DEFAULT '0' COMMENT '0 - sommerce, 1 - default, 2 - additional, 3 - subdomain',
  `ssl` tinyint(1) NOT NULL COMMENT '0 - disabled, 1 - enabled',
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_domain_store_id_idx` (`store_id`),
  KEY `idx_store_id_type` (`store_id`,`type`),
  KEY `domain_UNIQUE` (`domain`),
  CONSTRAINT `fk_domain_store_id` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 2018-07-06 12:02:26
