<?php

use yii\db\Migration;

/**
 * Class m181219_122829_add_paypal_payment_method
 */
class m181219_122829_add_paypal_payment_method extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            USE `" . DB_GATEWAYS . "`;

            INSERT INTO `payment_methods` (`id`, `method_name`) VALUES
            ('1',	'Paypal');
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
            USE `' . DB_GATEWAYS . '`;
            
            DELETE FROM `payment_methods`
            WHERE ((`id` = 1));
        ');
    }
}
