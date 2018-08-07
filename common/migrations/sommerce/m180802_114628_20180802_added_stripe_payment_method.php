<?php

use yii\db\Migration;

/**
 * Class m180802_114628_20180802_added_stripe_payment_method
 */
class m180802_114628_20180802_added_stripe_payment_method extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            
            INSERT INTO `payment_gateways` (`method`, `currencies`, `name`, `class_name`, `url`, `position`, `options`) VALUES 
            (\'stripe\', \'[\"RUB\", \"USD\"]\', \'Stripe\', \'Stripe\', \'stripe\', \'15\', \'{\"public_key\":\"\",\"secret_key\":\"\",\"webhook_secret\": \"\"}\');

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            
            DELETE FROM `payment_gateways`
            WHERE ((`method` = \'stripe\'));
        ');
    }
}
