USE `stores`;
CREATE TABLE `notification_default_templates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `code` varchar(255) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 - disabled, 1 - enabled',
  `position` int(11) NOT NULL,
  `recipient` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - admin, 2 - customer',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE='InnoDB';