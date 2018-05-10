<?php

use yii\db\Migration;

/**
 * Class m180508_085054_update_payment_gateways_paytr
 */
class m180508_085054_update_payment_gateways_paytr extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->execute('
            UPDATE `payment_gateways` SET
            `name` = \'Paytr\',
            `class_name` = \'Paytr\',
            `url` = \'paytr\',
            `position` = \'10\',
            `options` = \'{\\"merchant_id\\":\\"\\",\\"merchant_key\\":\\"\\",\\"merchant_salt\\":\\"\\",\\"commission\\":\\"\\"}\'
            WHERE `method` = \'paytr\';
        ');
    }

    public function down()
    {
    }
}
