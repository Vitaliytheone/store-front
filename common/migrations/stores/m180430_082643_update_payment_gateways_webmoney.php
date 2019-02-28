<?php

use yii\db\Migration;

/**
 * Class m180430_082643_update_payment_gateways_webmoney
 */
class m180430_082643_update_payment_gateways_webmoney extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->execute('
            UPDATE `payment_gateways` SET
            `name` = \'WebMoney\',
            `class_name` = \'Webmoney\',
            `url` = \'webmoney\',
            `position` = \'5\',
            `options` = \'{\\"purse\\":\\"\\",\\"secret_key\\":\\"\\",\\"test_mode\\":1}\'
            WHERE `method` = \'webmoney\';
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {

    }
}
