<?php

use yii\db\Migration;

/**
 * Class m180402_110556_store_template_database
 */
class m180402_110556_store_template_database extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180402_110556_store_template_database cannot be reverted.\n";

        return false;
    }

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

        $this->execute('
            CREATE DATABASE `store_template` DEFAULT CHARACTER SET = `utf8mb4`;
        ');


        $this->execute('    
            USE `store_template`;
            
            /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
            /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
            /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
            /*!40101 SET NAMES utf8 */;
            /*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
            /*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE=\'NO_AUTO_VALUE_ON_ZERO\' */;
            /*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
            
            
            # Дамп таблицы activity_log
            # ------------------------------------------------------------
            
            DROP TABLE IF EXISTS `activity_log`;
            
            CREATE TABLE `activity_log` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `admin_id` int(11) NOT NULL DEFAULT \'0\',
              `super_user` tinyint(1) NOT NULL DEFAULT \'0\',
              `created_at` int(11) NOT NULL DEFAULT \'0\',
              `ip` varchar(300) NOT NULL,
              `controller` varchar(300) NOT NULL,
              `action` varchar(300) NOT NULL,
              `request_data` mediumtext NOT NULL,
              `details` varchar(1000) DEFAULT NULL,
              `details_id` int(11) DEFAULT NULL,
              `event` int(11) NOT NULL DEFAULT \'0\',
              PRIMARY KEY (`id`),
              KEY `admin_id` (`admin_id`),
              KEY `event` (`event`),
              KEY `ip` (`ip`(191)),
              KEY `super_user` (`super_user`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            
            
            
            # Дамп таблицы blocks
            # ------------------------------------------------------------
            
            DROP TABLE IF EXISTS `blocks`;
            
            CREATE TABLE `blocks` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `code` varchar(300) NOT NULL,
              `content` mediumtext NOT NULL,
              `updated_at` int(11) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `code` (`code`(191))
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            
            
            
            # Дамп таблицы carts
            # ------------------------------------------------------------
            
            DROP TABLE IF EXISTS `carts`;
            
            CREATE TABLE `carts` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `key` varchar(64) DEFAULT NULL,
              `package_id` int(11) DEFAULT NULL,
              `link` varchar(255) DEFAULT NULL,
              `created_at` int(11) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `idx_key` (`key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            
            
            
            # Дамп таблицы checkouts
            # ------------------------------------------------------------
            
            DROP TABLE IF EXISTS `checkouts`;
            
            CREATE TABLE `checkouts` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `customer` varchar(255) DEFAULT NULL,
              `price` decimal(20,2) DEFAULT NULL,
              `status` tinyint(1) DEFAULT NULL COMMENT \'0 - pending\n1 - paid\n\',
              `method_status` varchar(255) DEFAULT NULL,
              `method_id` int(11) DEFAULT NULL,
              `ip` varchar(255) DEFAULT NULL,
              `details` mediumtext COMMENT \'json\nlink\nquantity\npackage_id\',
              `created_at` int(11) DEFAULT NULL,
              `updated_at` int(11) DEFAULT NULL,
              `currency` varchar(10) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            
            
            
            # Дамп таблицы custom_themes
            # ------------------------------------------------------------
            
            DROP TABLE IF EXISTS `custom_themes`;
            
            CREATE TABLE `custom_themes` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `name` varchar(300) NOT NULL,
              `folder` varchar(300) NOT NULL,
              `created_at` int(11) NOT NULL DEFAULT \'0\',
              `updated_at` int(11) NOT NULL DEFAULT \'0\',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            
            
            
            # Дамп таблицы files
            # ------------------------------------------------------------
            
            DROP TABLE IF EXISTS `files`;
            
            CREATE TABLE `files` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `type` tinyint(1) DEFAULT NULL COMMENT \'1 - logo, 2 - favicon, 3 - slider, 4 - features, 5 - review, 6 - steps\',
              `date` longtext,
              `created_at` int(11) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            
            
            
            # Дамп таблицы navigation
            # ------------------------------------------------------------
            
            DROP TABLE IF EXISTS `navigation`;
            
            CREATE TABLE `navigation` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `parent_id` tinyint(255) unsigned DEFAULT NULL,
              `name` varchar(300) DEFAULT NULL,
              `link` tinyint(2) unsigned DEFAULT NULL COMMENT \'1 - Home page, 2 - Products, 3 - Page, 4 - Web address\',
              `link_id` int(11) unsigned DEFAULT NULL,
              `position` tinyint(3) DEFAULT NULL,
              `url` varchar(255) DEFAULT NULL,
              `deleted` tinyint(1) NOT NULL DEFAULT \'0\',
              PRIMARY KEY (`id`),
              KEY `idx_parent_id_position` (`parent_id`,`position`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            
            
            
            # Дамп таблицы orders
            # ------------------------------------------------------------
            
            DROP TABLE IF EXISTS `orders`;
            
            CREATE TABLE `orders` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `checkout_id` int(11) DEFAULT NULL,
              `customer` varchar(255) DEFAULT NULL,
              `created_at` int(11) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `fk_orders_checkout_id` (`checkout_id`),
              CONSTRAINT `fk_orders_checkout_id` FOREIGN KEY (`checkout_id`) REFERENCES `checkouts` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            
            
            
            # Дамп таблицы packages
            # ------------------------------------------------------------
            
            DROP TABLE IF EXISTS `packages`;
            
            CREATE TABLE `packages` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(255) DEFAULT NULL,
              `price` decimal(10,2) DEFAULT NULL,
              `quantity` int(11) DEFAULT \'0\',
              `link_type` tinyint(1) DEFAULT \'0\',
              `product_id` int(11) DEFAULT \'0\',
              `visibility` tinyint(1) DEFAULT \'0\',
              `best` tinyint(1) DEFAULT NULL,
              `mode` tinyint(1) DEFAULT \'0\',
              `provider_id` int(11) DEFAULT \'0\',
              `provider_service` varchar(255) DEFAULT NULL,
              `deleted` tinyint(1) unsigned DEFAULT \'0\',
              `position` int(11) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `fk_packages_product_id` (`product_id`),
              KEY `idx_possition` (`position`),
              KEY `idx_product_id` (`product_id`),
              CONSTRAINT `fk_packages_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;
            
            
            
            # Дамп таблицы pages
            # ------------------------------------------------------------
            
            DROP TABLE IF EXISTS `pages`;
            
            CREATE TABLE `pages` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `title` varchar(255) DEFAULT NULL,
              `visibility` tinyint(1) DEFAULT NULL,
              `content` mediumtext,
              `seo_title` varchar(255) DEFAULT NULL,
              `seo_description` varchar(2000) DEFAULT NULL,
              `seo_keywords` varchar(2000) DEFAULT NULL,
              `url` varchar(255) DEFAULT NULL,
              `template` varchar(200) DEFAULT NULL,
              `deleted` tinyint(1) NOT NULL DEFAULT \'0\',
              `created_at` int(11) DEFAULT NULL,
              `updated_at` int(11) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `idx_deleted_visibility` (`deleted`,`visibility`),
              KEY `idx_deleted` (`deleted`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            
            LOCK TABLES `pages` WRITE;
            /*!40000 ALTER TABLE `pages` DISABLE KEYS */;
            
            INSERT INTO `pages` (`id`, `title`, `visibility`, `content`, `seo_title`, `seo_description`, `seo_keywords`, `url`, `template`, `deleted`, `created_at`, `updated_at`)
            VALUES
                (1,\'Contacts us\',1,\'\',\'Contacts us\',NULL,NULL,\'contacts\',\'contact\',0,NULL,NULL),
                (2,\'Terms of services\',1,\'\',\'Terms of services\',NULL,NULL,\'terms\',\'page\',0,NULL,NULL);
            
            /*!40000 ALTER TABLE `pages` ENABLE KEYS */;
            UNLOCK TABLES;
            
            
            # Дамп таблицы payments
            # ------------------------------------------------------------
            
            DROP TABLE IF EXISTS `payments`;
            
            CREATE TABLE `payments` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `checkout_id` int(11) DEFAULT NULL,
              `method` varchar(255) DEFAULT NULL,
              `customer` varchar(255) DEFAULT NULL,
              `amount` decimal(20,5) DEFAULT NULL,
              `status` tinyint(1) DEFAULT \'0\' COMMENT \'1 - Completed\n2 - Awating\n3 - Failed\n4 - Refunded\',
              `fee` decimal(20,5) DEFAULT \'0.00000\',
              `transaction_id` varchar(255) DEFAULT NULL,
              `memo` varchar(255) DEFAULT NULL,
              `response_status` varchar(255) DEFAULT NULL,
              `name` varchar(255) DEFAULT NULL,
              `email` varchar(255) DEFAULT NULL,
              `country` varchar(255) DEFAULT NULL,
              `created_at` int(11) DEFAULT NULL,
              `updated_at` int(11) DEFAULT \'0\',
              `currency` varchar(10) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `fk_payments_checkout_id` (`checkout_id`),
              KEY `idx_status` (`status`),
              KEY `idx_method` (`method`(191)),
              CONSTRAINT `fk_checkout_id` FOREIGN KEY (`checkout_id`) REFERENCES `checkouts` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            
            
            
            # Дамп таблицы payments_log
            # ------------------------------------------------------------
            
            DROP TABLE IF EXISTS `payments_log`;
            
            CREATE TABLE `payments_log` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `checkout_id` int(11) DEFAULT NULL,
              `result` longtext,
              `ip` varchar(255) DEFAULT NULL,
              `created_at` int(11) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `fk_paymens_log_checkout_id` (`checkout_id`),
              CONSTRAINT `id` FOREIGN KEY (`checkout_id`) REFERENCES `checkouts` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            
            
            
            # Дамп таблицы products
            # ------------------------------------------------------------
            
            DROP TABLE IF EXISTS `products`;
            
            CREATE TABLE `products` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(255) DEFAULT NULL,
              `position` int(11) DEFAULT \'0\',
              `url` varchar(255) DEFAULT NULL,
              `properties` varchar(1000) DEFAULT NULL,
              `description` mediumtext,
              `visibility` tinyint(1) DEFAULT NULL,
              `seo_title` varchar(300) DEFAULT NULL,
              `seo_description` varchar(1000) DEFAULT NULL,
              `seo_keywords` varchar(2000) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `idx_possition` (`position`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;
            
            
            
            # Дамп таблицы suborders
            # ------------------------------------------------------------
            
            DROP TABLE IF EXISTS `suborders`;
            
            CREATE TABLE `suborders` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `order_id` int(11) DEFAULT NULL,
              `checkout_id` int(11) DEFAULT NULL,
              `link` varchar(1000) DEFAULT NULL,
              `currency` varchar(10) DEFAULT NULL,
              `amount` decimal(20,5) DEFAULT NULL,
              `package_id` int(11) DEFAULT NULL,
              `quantity` int(11) DEFAULT \'0\',
              `status` tinyint(1) DEFAULT \'0\' COMMENT \'1 - Awaiting\n2 - Pending\n3 - In progress\n4 - Completed\n5 - Canceled\n6 - Failed\n7 - Error\',
              `updated_at` int(11) DEFAULT \'0\',
              `mode` tinyint(1) DEFAULT \'0\' COMMENT \'0 - manual, 1 - auto\',
              `send` tinyint(1) DEFAULT \'0\',
              `provider_id` int(11) DEFAULT NULL,
              `provider_service` varchar(300) DEFAULT NULL,
              `provider_order_id` varchar(300) DEFAULT NULL,
              `provider_charge` decimal(20,5) DEFAULT NULL,
              `provider_response` longtext,
              `provider_response_code` int(11) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `fk_order_item_checkout_id` (`checkout_id`),
              KEY `fk_order_item_order_id` (`order_id`),
              KEY `fk_order_item_package_id` (`package_id`),
              KEY `idx_order_id_mode` (`order_id`,`mode`),
              KEY `idx_mode` (`mode`),
              CONSTRAINT `fk_order_item_checkout_id` FOREIGN KEY (`checkout_id`) REFERENCES `checkouts` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
              CONSTRAINT `fk_order_item_package_id` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 
            
            /*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
            /*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
            /*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
            /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
            /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
            /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
        ');
    }

    public function down()
    {
        $this->execute('
            USE `store_template`;
            DROP DATABASE `store_template`;
        ');

        return false;
    }
}
