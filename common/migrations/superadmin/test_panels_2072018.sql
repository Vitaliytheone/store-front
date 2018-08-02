-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE `additional_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `res` int(11) NOT NULL,
  `apihelp` varchar(2000) NOT NULL,
  `content` longtext NOT NULL,
  `type` int(11) NOT NULL COMMENT 'Тип процессора 0 - не gyp, 1 - gyp',
  `status` int(11) NOT NULL COMMENT 'Статус работы 0 - работает 1 - не работает, 2 - не доделан',
  `search` int(11) NOT NULL,
  `username` varchar(300) NOT NULL,
  `password` varchar(300) NOT NULL,
  `skype` varchar(300) NOT NULL,
  `type_name` varchar(300) NOT NULL,
  `sc` int(11) NOT NULL,
  `callback` tinyint(1) NOT NULL,
  `refill` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - нет, 1 - основной параметр, 2 - дополнительный параметр',
  `cancel` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - нет, 1 - основной параметр, 2 - дополнительный параметр',
  `auto_services` int(11) NOT NULL COMMENT '0 - not auto list, 1 - auto list, 2 - custom string',
  `auto_order` int(11) NOT NULL DEFAULT '1',
  `processing` int(11) NOT NULL DEFAULT '1',
  `show_id` int(11) NOT NULL DEFAULT '1',
  `input_type` int(11) NOT NULL,
  `proxy` varchar(1000) NOT NULL,
  `string_type` int(11) NOT NULL,
  `string_name` int(11) NOT NULL COMMENT '0 - String, 1 - Service, 2 - type, 3 - serviceID, 4 - id, 5 - service, 6 - product_id, 7 - service_id, 8 - string, 9 - category, 10 - ordertype, 11 - package_id, 12 - methodname, 13 - , 14 - orderType, 15 - services, 16 - serviceid, 17 - id_service, 18 - SERVICEID, 19 - o_type, 20 - act, 21 - order_service, 22 - product, 23 - tid, 24 - order_service',
  `params` longtext NOT NULL,
  `type_services` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `send_method` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - perfectpanel, 1 - default sender, 2 - multicurl sender, 3 - multiaddorder',
  PRIMARY KEY (`id`),
  KEY `res` (`res`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(300) NOT NULL,
  `password` varchar(64) NOT NULL,
  `first_name` varchar(300) NOT NULL,
  `last_name` varchar(300) NOT NULL,
  `access_token` varchar(64) NOT NULL,
  `token` varchar(32) NOT NULL,
  `status` int(11) NOT NULL,
  `child_panels` tinyint(1) NOT NULL,
  `stores` tinyint(1) NOT NULL DEFAULT '0',
  `buy_domain` tinyint(1) DEFAULT '0',
  `date_create` int(11) NOT NULL,
  `auth_date` int(11) NOT NULL,
  `auth_ip` varchar(100) NOT NULL,
  `timezone` int(11) NOT NULL,
  `auth_token` varchar(64) NOT NULL,
  `unpaid_earnings` decimal(20,5) NOT NULL DEFAULT '0.00000',
  `referrer_id` int(11) unsigned DEFAULT NULL,
  `referral_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 - not active, 1 - active, 2 - blocked',
  `paid` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 - not paid, 1 - paid',
  `referral_link` varchar(5) DEFAULT NULL,
  `referral_expired_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `referrer_id` (`referrer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `domains` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `zone_id` int(11) NOT NULL,
  `contact_id` varchar(250) NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '1 – ok, 2 - expired',
  `domain` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `created_at` int(11) NOT NULL,
  `expiry` int(11) NOT NULL,
  `privacy_protection` tinyint(1) NOT NULL,
  `transfer_protection` tinyint(1) NOT NULL,
  `details` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `zone_id` (`zone_id`),
  KEY `idx_status_created_at` (`status`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL,
  `status` int(11) NOT NULL COMMENT '1 - pending; 2 - paid;3 - added; 4 - canceled',
  `hide` tinyint(1) DEFAULT '0',
  `processing` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `ip` varchar(300) NOT NULL,
  `domain` varchar(300) DEFAULT NULL,
  `details` text NOT NULL,
  `item` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1 - buy panel; 2 - buy domain; 3 - buy ssl, 4 - buy child panel',
  `item_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `domain` (`domain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


SET NAMES utf8mb4;

CREATE TABLE `params` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(64) NOT NULL,
  `options` text NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL,
  `site` varchar(1000) NOT NULL,
  `name` varchar(1000) NOT NULL,
  `subdomain` int(11) NOT NULL,
  `skype` varchar(1000) NOT NULL,
  `expired` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `act` int(11) NOT NULL COMMENT '0 - frozen; 1 - active; 2 - terminated; 3 - pending; 4 - canceled',
  `hide` tinyint(1) DEFAULT '0',
  `child_panel` tinyint(1) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `folder` varchar(6) NOT NULL,
  `folder_content` text NOT NULL,
  `theme` int(11) NOT NULL,
  `theme_custom` varchar(300) NOT NULL,
  `theme_default` varchar(300) NOT NULL,
  `ssl` int(11) NOT NULL,
  `theme_path` varchar(500) NOT NULL,
  `rtl` tinyint(1) NOT NULL DEFAULT '0',
  `utc` int(11) NOT NULL,
  `db` varchar(300) NOT NULL,
  `apikey` varchar(64) NOT NULL,
  `orders` int(11) NOT NULL,
  `plan` int(11) NOT NULL,
  `tariff` int(11) NOT NULL,
  `last_count` int(11) NOT NULL,
  `current_count` int(11) NOT NULL,
  `forecast_count` int(11) NOT NULL,
  `paypal` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `lang` varchar(32) NOT NULL,
  `language_id` int(11) NOT NULL,
  `currency` int(11) NOT NULL,
  `seo` int(11) NOT NULL,
  `comments` int(11) NOT NULL,
  `mentions` int(11) NOT NULL,
  `mentions_wo_hashtag` int(11) NOT NULL,
  `mentions_custom` int(11) NOT NULL,
  `mentions_hashtag` int(11) NOT NULL,
  `mentions_follower` int(11) NOT NULL,
  `mentions_likes` int(11) NOT NULL,
  `writing` int(11) NOT NULL,
  `drip_feed` tinyint(4) NOT NULL COMMENT '0 - Disabled, 1 - Enabled',
  `userpass` int(11) NOT NULL,
  `validation` int(11) NOT NULL,
  `start_count` int(11) NOT NULL,
  `getstatus` int(11) NOT NULL,
  `custom` int(11) NOT NULL,
  `custom_header` text NOT NULL,
  `custom_footer` text NOT NULL,
  `seo_title` varchar(3000) NOT NULL,
  `seo_desc` varchar(3000) NOT NULL,
  `seo_key` varchar(3000) NOT NULL,
  `package` int(11) NOT NULL,
  `captcha` int(11) NOT NULL COMMENT '0 - on, 1 - off',
  `logo` varchar(300) NOT NULL,
  `favicon` varchar(300) NOT NULL,
  `public_service_list` int(11) NOT NULL,
  `ticket_system` int(11) NOT NULL,
  `registration_page` int(11) NOT NULL,
  `terms_checkbox` int(11) NOT NULL,
  `skype_field` int(11) NOT NULL,
  `name_fields` tinyint(1) NOT NULL DEFAULT '0',
  `name_modal` tinyint(1) NOT NULL DEFAULT '0',
  `service_description` int(11) NOT NULL,
  `service_categories` int(11) NOT NULL,
  `last_payment` int(11) NOT NULL,
  `ticket_per_user` int(11) NOT NULL,
  `auto_order` int(11) NOT NULL,
  `currency_format` int(11) NOT NULL,
  `tasks` tinyint(1) NOT NULL DEFAULT '0',
  `custom_header_last` text NOT NULL,
  `custom_footer_last` text NOT NULL,
  `hash_method` tinyint(4) NOT NULL DEFAULT '0' COMMENT '(0 - md5, 1 - bcrypt)',
  `hash_salt` varchar(64) DEFAULT NULL,
  `notification_email` varchar(300) NOT NULL,
  `forgot_password` tinyint(1) NOT NULL COMMENT '0 - disabled, 1 - enabled',
  `no_invoice` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - disabled, 1 - enabled',
  `js_error_tracking` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - disabled, 1 - enabled',
  PRIMARY KEY (`id`),
  KEY `site` (`site`(255)),
  KEY `act` (`act`),
  KEY `idx_cid` (`cid`),
  KEY `idx_act_date_child_panel` (`act`,`date`,`child_panel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `ssl_cert` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL,
  `project_type` tinyint(1) DEFAULT NULL COMMENT '1 - Panel, 2 - Store',
  `pid` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `status` tinyint(2) NOT NULL COMMENT '0 - Pending; 4 - Cancel; 1 - Active; 2 - Processing; 3 - Processing(payment needed); 5 - Incomplete; 6 - Expiry',
  `checked` tinyint(1) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `csr_code` text,
  `csr_key` text,
  `details` text NOT NULL,
  `expiry` varchar(10) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `fk_ssl_cert__customers` (`cid`),
  KEY `idx_status_created_at` (`status`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `super_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `created_at` int(11) NOT NULL,
  `first_name` varchar(250) NOT NULL,
  `last_name` varchar(250) NOT NULL,
  `last_login` varchar(250) DEFAULT NULL,
  `last_ip` varchar(250) DEFAULT NULL,
  `auth_key` varchar(250) NOT NULL,
  `rules` varchar(1000) DEFAULT NULL,
  `status` tinyint(4) NOT NULL COMMENT '0 – suspended; 1 - active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `super_admin` (`id`, `username`, `password`, `created_at`, `first_name`, `last_name`, `last_login`, `last_ip`, `auth_key`, `rules`, `status`) VALUES
(1,	'account',	'db8fdd9fc4015a183dba173bff7ed15d17ced79b63f6963cb8cb0de839eb6dc9',	1494255109,	'firstname',	'lastname',	'1530521139',	'::1',	'tTLdtO2omV7euKD_Q2Yy6lO6oVQVWfmg',	'{\"panels\":\"1\",\"orders\":\"1\",\"domains\":\"1\",\"ssl\":\"1\",\"customers\":\"1\",\"invoices\":\"1\",\"payments\":\"1\",\"tickets\":\"1\",\"providers\":\"1\",\"reports\":\"1\",\"logs\":0,\"staffs\":\"1\",\"settings\":\"1\",\"referrals\":\"1\",\"logs\":\"1\",\"tools\":\"1\"}',	1);

CREATE TABLE `super_admin_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `super_admin_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 - panels, 1 - my, 2 - sommerce admin',
  `token` varchar(64) NOT NULL,
  `expiry_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `super_log` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(250) NOT NULL,
  `params` varchar(1000) NOT NULL,
  `ip` varchar(250) NOT NULL,
  `user_agent` varchar(1000) NOT NULL,
  `created_at` int(11) NOT NULL,
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `super_log_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `super_admin` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `super_tools_scanner` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `panel_id` int(11) unsigned DEFAULT NULL COMMENT 'Separated ids for each panels',
  `panel` tinyint(2) unsigned DEFAULT NULL COMMENT '1-Levopanel, 2-Smmfire',
  `domain` varchar(255) DEFAULT NULL,
  `server_ip` varchar(50) DEFAULT NULL,
  `status` tinyint(2) DEFAULT NULL COMMENT '1 Active, 2 Disabled, 3 Perfectpanel, 4 Not_resolved, 5 Moved, 6 Deleted',
  `details` varchar(1000) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `third_party_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item` tinyint(4) NOT NULL COMMENT '1 – buy panel, 2 – prolongation panel, 3 – buy domain, 4 – prolongation domain, 5 – buy ssl certification, 6 – prolongation ssl certification; 7 - order',
  `item_id` int(11) NOT NULL,
  `code` varchar(32) DEFAULT NULL,
  `details` text NOT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `subject` varchar(300) NOT NULL,
  `message` varchar(1000) NOT NULL,
  `admin` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `status` int(11) NOT NULL COMMENT '0 - pending; 1 - respinded; 2 - closed; 3 - in progress; 4 - Solved;',
  `date` int(11) NOT NULL,
  `date_update` int(11) NOT NULL,
  `ip` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `ticket_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `message` varchar(1000) NOT NULL,
  `date` int(11) NOT NULL,
  `ip` varchar(300) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2018-07-02 09:16:46