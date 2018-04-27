<?php

use yii\db\Migration;

/**
 * Class m180427_085307_updated_payment_gateways_table
 */
class m180427_085307_updated_payment_gateways_table extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->execute("
            ALTER TABLE `payment_gateways`
            ADD `class_name` varchar(255) NOT NULL AFTER `name`,
            ADD `url` varchar(255) NOT NULL AFTER `class_name`,
            ADD `position` tinyint(2) NOT NULL AFTER `url`,
            ADD `options` text NOT NULL AFTER `position`;
        ");

        $this->execute('
            UPDATE `payment_gateways` SET
            `name` = \'PayPal\',
            `class_name` = \'Paypal\',
            `url` = \'paypal\',
            `position` = \'1\',
            `options` = \'{\\"email\\":\\"\\",\\"username\\":\\"\\",\\"password\\":\\"\\",\\"signature\\":\\"\\",\\"test_mode\\":\\"\\"}\'
            WHERE `method` = \'paypal\';
            
            UPDATE `payment_gateways` SET
            `name` = \'2Checkout\',
            `class_name` = \'Twocheckout\',
            `url` = \'2checkout\',
            `position` = \'2\',
            `options` = \'{\\"account_number\\":\\"\\",\\"secret_word\\":\\"\\",\\"test_mode\\":\\"\\"}\'
            WHERE `method` = \'2checkout\';
            
            UPDATE `payment_gateways` SET
            `name` = \'CoinPayments\',
            `class_name` = \'Coinpayments\',
            `url` = \'coinpayments\',
            `position` = \'3\',
            `options` = \'{\\"merchant_id\\":\\"\\",\\"ipn_secret\\":\\"\\",\\"test_mode\\":\\"\\"}\'
            WHERE `method` = \'coinpayments\';
        ');
    }

    public function down()
    {
        $this->execute("
            ALTER TABLE `payment_gateways`
            DROP `class_name`,
            DROP `url`,
            DROP `position`,
            DROP `options`;
        ");
    }
}
