<?php

use yii\db\Migration;

/**
 * Class m180430_094206_update_payment_gateways_free_kassa
 */
class m180430_094206_update_payment_gateways_free_kassa extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->execute('
            UPDATE `payment_gateways` SET
            `name` = \'Free-Kassa\',
            `class_name` = \'Freekassa\',
            `url` = \'freekassa\',
            `position` = \'6\',
            `options` = \'{\\"merchant_id\\":\\"\\",\\"secret_word\\":\\"\\",\\"secret_word2\\":\\"\\",\\"test_mode\\":1}\'
            WHERE `method` = \'freekassa\';
        ');
    }

    public function down()
    {
    }

}
