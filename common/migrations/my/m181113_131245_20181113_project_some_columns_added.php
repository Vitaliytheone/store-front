<?php

use yii\db\Migration;

/**
 * Class m181113_131245_20181113_project_some_columns_added
 */
class m181113_131245_20181113_project_some_columns_added extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `project` ADD `whois_lookup` TEXT  NULL  COMMENT \'Json domain data\'  AFTER `refiller`;');
        $this->execute('ALTER TABLE `project` ADD `nameservers` TEXT  NULL COMMENT \'Json domain nameservers data\'  AFTER `whois_lookup`;');
        $this->execute('ALTER TABLE `project` ADD `dns_checked_at` INT(11)  UNSIGNED  NULL  COMMENT \'Last dns-check timestamp\'  AFTER `nameservers`;');
        $this->execute('ALTER TABLE `project` ADD `dns_status` TINYINT(1)  UNSIGNED  NULL  DEFAULT NULL  COMMENT \'dns-check result: null-неизвестно, 0-не наши ns, 1-наш ns\'  AFTER `dns_checked_at`;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('ALTER TABLE `ssl_cert` DROP `whois_lookup`;');
        $this->execute('ALTER TABLE `ssl_cert` DROP `nameservers`;');
        $this->execute('ALTER TABLE `ssl_cert` DROP `dns_checked_at`;');
        $this->execute('ALTER TABLE `ssl_cert` DROP `dns_status`;');
    }
}
