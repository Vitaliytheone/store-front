<?php

use yii\db\Migration;

/**
 * Class m181214_133420_init_gateway_tables
 */
class m181214_133420_init_gateway_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->execute("
            USE `" . DB_GATEWAY . "`;

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
              `template_name` varchar(200) DEFAULT NULL,
              `deleted` tinyint(1) NOT NULL DEFAULT '0',
              `is_default` tinyint(1) NOT NULL DEFAULT '0',
              `created_at` int(11) DEFAULT NULL,
              `updated_at` int(11) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `idx_deleted_visibility` (`deleted`,`visibility`),
              KEY `idx_deleted` (`deleted`)
            ) ENGINE=InnoDB;
            
            
            DROP TABLE IF EXISTS `themes_files`;
            CREATE TABLE `themes_files` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `theme_id` int(11) NOT NULL,
              `name` varchar(300) NOT NULL,
              `content` text NOT NULL,
              `created_at` int(11) NOT NULL,
              `updated_at` int(11) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->execute("
            USE `" . DB_GATEWAY . "`;

            DROP TABLE IF EXISTS `pages`;
            DROP TABLE IF EXISTS `themes_files`;
        ");
    }
}
