<?php

use yii\db\Migration;

/**
 * Class m180503_090833_update_payment_gateways_paywant
 */
class m180503_090833_update_payment_gateways_paywant extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->execute('
            UPDATE `payment_gateways` SET
            `name` = \'Paywant\',
            `class_name` = \'Paywant\',
            `url` = \'paywant\',
            `position` = \'8\',
            `options` = \'{\\"apiKey\\":\\"\\",\\"apiSecret\\":\\"\\",\\"fee\\":1}\'
            WHERE `method` = \'paywant\';
        ');
    }

    public function down()
    {
    }
}
