<?php

use yii\db\Migration;

/**
 * Class m190115_131939_add_stripe_payment_method
 */
class m190115_131939_add_stripe_payment_method extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            USE `" . DB_GATEWAYS . "`;

            INSERT INTO `payment_methods` (`id`, `method_name`, `class_name`, `url`) VALUES
            ('2',	'Stripe', 'Stripe', 'stripe');
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
            WHERE ((`method_name` = \'Stripe\'));
        ');
    }
}
