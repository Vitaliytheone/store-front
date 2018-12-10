<?php

use yii\db\Migration;

/**
 * Class m181210_065817_store_add_paypalstandard_payment_method
 */
class m181210_065817_store_add_paypalstandard_payment_method extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            
            INSERT INTO `payment_gateways` (`id`, `method`, `currencies`, `name`, `class_name`, `url`, `position`, `options`, `visibility`)
            VALUES (NULL, \'paypalstandard\', \'[\"USD\",\"AUD\",\"BRL\",\"CAD\",\"CZK\",\"DKK\",\"EUR\",\"HKD\",\"HUF\",\"ILS\",\"JPY\",\"MYR\",\"MXN\",\"NZD\",\"NOK\",\"PHP\",\"PLN\",\"GBP\",\"RUB\",\"SGD\",\"SEK\",\"CHF\",\"TWD\",\"THB\",\"INR\",\"IDR\"]\',
            \'PayPal Standard\', \'Paypalstandard\', \'paypalstandard\', \'16\', \'{\\\"email\\\":\\\"\\\",\\\"username\\\":\\\"\\\",\\\"password\\\":\\\"\\\",\\\"signature\\\":\\\"\\\",\\\"test_mode\\\":\\\"\\\"}\', \'0\');

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
            WHERE ((`method` = \'paypalstandard\'));
        ');
    }
}
