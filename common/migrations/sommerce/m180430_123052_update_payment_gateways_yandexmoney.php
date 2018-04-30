<?php

use yii\db\Migration;

/**
 * Class m180430_123052_update_payment_gateways_yandexmoney
 */
class m180430_123052_update_payment_gateways_yandexmoney extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->execute('
            UPDATE `payment_gateways` SET
            `name` = \'Yandex.Money\',
            `class_name` = \'Yandexmoney\',
            `url` = \'yandexmoney\',
            `position` = \'7\',
            `options` = \'{\\"wallet_number\\":\\"\\",\\"secret_word\\":\\"\\",\\"test_mode\\":1}\'
            WHERE `method` = \'yandexmoney\';
        ');
    }

    public function down()
    {

    }
}
