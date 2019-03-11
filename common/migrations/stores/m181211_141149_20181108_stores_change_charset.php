<?php

use yii\db\Migration;

/**
 * Class m181211_141149_20181108_stores_change_charset
 */
class m181211_141149_20181108_stores_change_charset extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute($this->getQuery());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }

    /**
     * Get SQL query
     * @return string
     */
    public function getQuery()
    {
        return 'USE `' . DB_STORES . '`;
                ALTER TABLE `stores_send_orders` DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;
                ALTER TABLE `stores` DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;
                
                ALTER TABLE `stores_send_orders`
                  CHANGE `store_db` `store_db` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;
                
                ALTER TABLE `stores`
                  CHANGE `domain` `domain` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                  CHANGE `ssl` `ssl` VARCHAR(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT \'0\',
                  CHANGE `name` `name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                  CHANGE `language` `language` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                  CHANGE `db_name` `db_name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                  CHANGE `logo` `logo` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                  CHANGE `favicon` `favicon` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                  CHANGE `currency` `currency` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                  CHANGE `seo_title` `seo_title` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                  CHANGE `seo_keywords` `seo_keywords` VARCHAR(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                  CHANGE `seo_description` `seo_description` VARCHAR(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                  CHANGE `folder` `folder` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                  CHANGE `folder_content` `folder_content` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                  CHANGE `theme_name` `theme_name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                  CHANGE `theme_folder` `theme_folder` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                  CHANGE `custom_header` `custom_header` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                  CHANGE `custom_footer` `custom_footer` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                  CHANGE `whois_lookup` `whois_lookup` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT \'Json domain data\',
                  CHANGE `nameservers` `nameservers` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT \'Json domain nameservers data\',
                  CHANGE `dns_checked_at` `dns_checked_at` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT \'Last dns-check timestamp\',
                  CHANGE `dns_status` `dns_status` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT \'dns-check result: null-неизвестно, 0-не наши ns, 1-наш ns\';';
    }
}
