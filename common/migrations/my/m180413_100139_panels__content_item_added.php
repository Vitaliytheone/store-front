<?php

use yii\db\Migration;

/**
 * Class m180413_100139_panels__notification_email_new_mssage_added
 */
class m180413_100139_panels__content_item_added extends Migration
{
    public function up()
    {
        $this->execute('
            USE `' . DB_PANELS . '`;
            INSERT INTO `content` (`name`, `text`, `updated_at`) VALUES (\'paypal_verify_note\', \'Payment verification needed! Check your email\', \'0\');
        ');
    }

    public function down()
    {
        $this->execute('
            USE `' . DB_PANELS . '`
            DELETE FROM `content` WHERE `name` = "paypal_verify_note";
        ');
    }
}
