<?php

use yii\db\Migration;

/**
 * Class m181221_085523_update_paypal_payment_method
 */
class m181221_085523_update_paypal_payment_method extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            USE `" . DB_GATEWAYS . "`;

            UPDATE `payment_methods` SET
                `method_name` = 'Paypal',
                `class_name` = 'Paypal',
                `url` = 'paypal'
                WHERE `id` = '1';
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
