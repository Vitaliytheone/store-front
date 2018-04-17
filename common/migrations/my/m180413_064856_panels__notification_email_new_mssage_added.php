<?php

use yii\db\Migration;

/**
 * Class m180413_064856_panels__notification_email_new_mssage_added
 */
class m180413_064856_panels__notification_email_new_mssage_added extends Migration
{
    public function up()
    {
        $this->execute('
            USE `' . DB_PANELS . '`;
            INSERT INTO `notification_email` (`subject`, `message`, `code`, `enabled`)
            VALUES (\'Paypal payment verification required\', \'To confirm the authenticity of your billing account, please click on the link below. \n \{\{verify_link\}\}\', \'paypal_verify\', 1);
        ');
    }

    public function down()
    {
        $this->execute('
            USE `' . DB_PANELS . '`;
            DELETE FROM `notification_email` WHERE `code` = "paypal_verify";
        ');

        return false;
    }
}
