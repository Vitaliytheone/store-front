<?php

use yii\db\Migration;

/**
 * Class m180504_092602_update_payment_gateways_billplz
 */
class m180504_092602_update_payment_gateways_billplz extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->execute('
            UPDATE `payment_gateways` SET
            `name` = \'Billplz\',
            `class_name` = \'Billplz\',
            `url` = \'billplz\',
            `position` = \'9\',
            `options` = \'{\\"collectionId\\":\\"\\",\\"secret\\":\\"\\"}\'
            WHERE `method` = \'billplz\';
        ');
    }

    public function down()
    {
    }
}
