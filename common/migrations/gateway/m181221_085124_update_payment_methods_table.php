<?php

use yii\db\Migration;

/**
 * Class m181221_085124_update_payment_methods_table
 */
class m181221_085124_update_payment_methods_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            USE `" . DB_GATEWAYS . "`;

            ALTER TABLE `payment_methods`
                ADD `class_name` varchar(300) NOT NULL AFTER `method_name`,
                ADD `url` varchar(300) NOT NULL AFTER `class_name`;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
            USE `' . DB_GATEWAYS . '`;
            
            ALTER TABLE `payment_methods`
                DROP `class_name`,
                DROP `url`;
        ');
    }
}
