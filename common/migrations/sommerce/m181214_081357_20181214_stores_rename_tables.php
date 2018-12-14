<?php

use yii\db\Migration;

/**
 * Class m181214_081357_20181214_stores_rename_tables
 */
class m181214_081357_20181214_stores_rename_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameTable(DB_STORES . '.payment_methods', 'store_payment_methods');

        $this->renameTable(DB_STORES . '.payment_gateways', 'payment_methods');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameTable(DB_STORES . '.payment_methods', 'payment_gateways');

        $this->renameTable(DB_STORES . '.store_payment_methods', 'payment_methods');
    }
}
