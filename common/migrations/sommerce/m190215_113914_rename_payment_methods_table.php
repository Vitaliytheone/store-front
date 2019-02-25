<?php

use yii\db\Migration;

/**
 * Class m190215_113914_rename_payment_methods_table
 */
class m190215_113914_rename_payment_methods_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            
            ALTER TABLE payment_methods RENAME payment_methods_last;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            
            ALTER TABLE payment_methods_last RENAME payment_methods;
        ');
    }

}
