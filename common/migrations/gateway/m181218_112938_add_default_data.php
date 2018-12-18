<?php

use yii\db\Migration;

/**
 * Class m181218_112938_add_default_data
 */
class m181218_112938_add_default_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            USE " . DB_GATEWAY . ";
            
            INSERT INTO `admins` (`id`, `site_id`, `username`, `password`, `auth_key`, `status`, `ip`, `last_login`, `created_at`, `updated_at`) VALUES
            (1,	1,	'admin',	'b8debceae0c4b8a60048e41d3b90c451bb437c4a157f8e550c2958fec15e9edc',	'3807e30f5a8aae7b82c562413481736792b5c4be09829214812d2105d02bd2d3',	1,	'::1',	1545052540,	1516106224,	1544787825);
            
            
            
            INSERT INTO `sites` (`id`, `customer_id`, `domain`, `subdomain`, `ssl`, `db_name`, `seo_title`, `seo_keywords`, `seo_description`, `folder`, `folder_content`, `theme_name`, `theme_folder`, `whois_lookup`, `nameservers`, `dns_status`, `dns_checked_at`, `expired_at`, `created_at`, `updated_at`) VALUES
            (1,	1,	'gateway.v1',	0,	0,	'gateway_site',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	0,	NULL,	2147483647,	0,	NULL);

        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("
            USE " . DB_GATEWAY . ";
            
            SET foreign_key_checks = 0;
            TRUNCATE TABLE `admins`;
            TRUNCATE TABLE `default_themes`;
            TRUNCATE TABLE `payment_methods`;
            TRUNCATE TABLE `sites`;
            TRUNCATE TABLE `site_payment_methods`;
            TRUNCATE TABLE `system_migrations`;
        ");
    }
}
