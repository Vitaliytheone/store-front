<?php

use yii\db\Migration;

/**
 * Class m180427_122203_add_payment_gateways_pagseguro
 */
class m180427_122203_add_payment_gateways_pagseguro extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->execute('
            INSERT INTO `payment_gateways` (`method`, `name`, `class_name`, `url`, `position`, `options`, `currencies`) VALUES
            (\'pagseguro\',	\'PagSeguro\',	\'Pagseguro\',	\'pagseguro\',	4,	\'{\"email\":\"\",\"token\":\"\",\"test_mode\":1}\',	\'[\"BRL\"]\');
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->execute('
            DELETE FROM `payment_gateways`
            WHERE ((`method` = \'pagseguro\'));
        ');
    }
}
