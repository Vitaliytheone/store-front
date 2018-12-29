<?php

use yii\db\Migration;

/**
 * Class m181227_091302_add_sites_table_indexes
 */
class m181227_091302_add_sites_table_indexes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            USE `" . DB_GATEWAYS . "`;

            ALTER TABLE `sites`
                CHANGE `customer_id` `customer_id` int(11) unsigned NOT NULL AFTER `id`;
                
            ALTER TABLE `sites`
                ADD INDEX `idx_customer_id` (`customer_id`);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
            USE `' . DB_GATEWAYS . '`;
            
            ALTER TABLE `sites`
                DROP INDEX `idx_customer_id`;
                
            ALTER TABLE `sites`
                CHANGE `customer_id` `customer_id` int(11) NOT NULL AFTER `id`;
        ');
    }
}
