USE `gateways`;

SET foreign_key_checks = 0;

DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `auth_key` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - active; 2 - suspended',
  `ip` varchar(255) DEFAULT NULL,
  `last_login` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`),
  CONSTRAINT `fk_admins__sites` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`)
) ENGINE=InnoDB;


DROP TABLE IF EXISTS `default_themes`;
CREATE TABLE `default_themes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(300) NOT NULL,
  `folder` varchar(300) NOT NULL,
  `position` int(11) NOT NULL,
  `thumbnail` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;


DROP TABLE IF EXISTS `payment_methods`;
CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `method_name` varchar(300) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;


DROP TABLE IF EXISTS `sites`;
CREATE TABLE `sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `subdomain` tinyint(1) NOT NULL DEFAULT '0',
  `ssl` tinyint(1) NOT NULL DEFAULT '0',
  `db_name` varchar(255) NOT NULL,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_keywords` varchar(2000) DEFAULT NULL,
  `seo_description` varchar(2000) DEFAULT NULL,
  `folder` varchar(255) DEFAULT NULL,
  `folder_content` varchar(255) DEFAULT NULL,
  `theme_name` varchar(255) DEFAULT NULL,
  `theme_folder` varchar(255) DEFAULT NULL,
  `whois_lookup` text,
  `nameservers` text,
  `dns_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'dns-check result: null-неизвестно, 0-не наши ns, 1-наш ns',
  `dns_checked_at` int(11) DEFAULT NULL,
  `expired_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;


DROP TABLE IF EXISTS `site_payment_methods`;
CREATE TABLE `site_payment_methods` (
  `id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  `method_id` int(11) NOT NULL,
  `options` text NOT NULL,
  `visibility` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) DEFAULT NULL,
  KEY `site_id` (`site_id`),
  KEY `method_id` (`method_id`),
  CONSTRAINT `fk_site_payment_methods__sites` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`),
  CONSTRAINT `fk_site_payment_methods__payment_methods` FOREIGN KEY (`method_id`) REFERENCES `payment_methods` (`id`)
) ENGINE=InnoDB;