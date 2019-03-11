<?php

use yii\db\Migration;

/**
 * Class m180427_150043_update_payment_gateways_pagseguro
 */
class m180427_150043_update_payment_gateways_pagseguro extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->execute('
            UPDATE `payment_gateways` SET
            `name` = \'PagSeguro\',
            `class_name` = \'Pagseguro\',
            `url` = \'pagseguro\',
            `position` = \'4\',
            `options` = \'{\"email\":\"\",\"token\":\"\",\"test_mode\":1}\'
            WHERE `method` = \'pagseguro\';
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
    }
}
