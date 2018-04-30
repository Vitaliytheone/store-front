<?php

use yii\db\Migration;

/**
 * Class m180430_125552_update_payment_gateways_paypal
 */
class m180430_125552_update_payment_gateways_paypal extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->execute('
             UPDATE `payment_gateways` SET
            `name` = \'PayPal\',
            `class_name` = \'Paypal\',
            `url` = \'paypalexpress\',
            `position` = \'1\',
            `options` = \'{\\"email\\":\\"\\",\\"username\\":\\"\\",\\"password\\":\\"\\",\\"signature\\":\\"\\",\\"test_mode\\":\\"\\"}\'
            WHERE `method` = \'paypal\';
        ');
    }

    public function down()
    {

    }
}
