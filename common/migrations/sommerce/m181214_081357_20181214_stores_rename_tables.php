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
        $this->execute('
            ALTER TABLE payment_methods RENAME store_payment_methods;

            ALTER TABLE payment_gateways RENAME payment_methods;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
            ALTER TABLE payment_methods RENAME payment_gateways;

            ALTER TABLE store_payment_methods RENAME payment_methods;
        ');
    }
}
