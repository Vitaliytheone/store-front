<?php

use yii\db\Migration;

/**
 * Class m181214_071756_stores_add_mollie_payment_method
 */
class m181214_071756_stores_add_mollie_payment_method extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            
            INSERT INTO `payment_gateways` (`id`, `method`, `currencies`, `name`, `class_name`, `url`, `position`, `options`, `visibility`)
            VALUES (NULL, \'mollie\', \'[\"USD\",\"AUD\",\"BRL\",\"CAD\",\"CZK\",\"DKK\",\"EUR\",\"HKD\",\"HUF\",\"ILS\",\"JPY\",\"MYR\",\"MXN\",\"NZD\",\"NOK\",\"PHP\",\"PLN\",\"GBP\",\"RUB\",\"SGD\",\"SEK\",\"CHF\",\"TWD\",\"THB\",\"INR\",\"IDR\"]\',
             \'Mollie\', \'Mollie\', \'mollie\', \'17\', \'{\"secret_key\":\"\"}\', \'1\');

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
            WHERE ((`method` = \'mollie\'));
        ');
    }
}
