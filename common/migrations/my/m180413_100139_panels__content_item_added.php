<?php

use yii\db\Migration;

/**
 * Class m180413_100139_panels__notification_email_new_mssage_added
 */
class m180413_100139_panels__content_item_added extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180413_100139_panels__notification_email_new_mssage_added cannot be reverted.\n";

        return false;
    }

    public function up()
    {
        $this->execute('
            USE `panels`;
            INSERT INTO `content` (`name`, `text`, `updated_at`) VALUES (\'paypal_verify_note\', \'Payment verification needed! Check your email\', \'0\');
        ');
    }

    public function down()
    {
        $this->execute('
            USE `panels`
            DELETE FROM `content` WHERE `name` = "paypal_verify_note";
        ');
    }
}
