<?php

use yii\db\Migration;

/**
 * Class m180413_064856_panels__notification_email_new_mssage_added
 */
class m180413_064856_panels__notification_email_new_mssage_added extends Migration
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
        echo "m180413_064856_panels__notification_email_new_mssage_added cannot be reverted.\n";

        return false;
    }

    public function up()
    {
        $this->execute('
            USE `panels`;
            INSERT INTO `notification_email` (`subject`, `message`, `code`, `enabled`)
            VALUES
              (\'Paypal payment verification required\', \'To confirm the authenticity of your billing account, please click on the link below. \n{{verify_link}}\', \'paypal_verify\', 1);
        ');
    }

    public function down()
    {
        $this->execute('
            USE `panels`;
            DELETE FROM `notification_email` WHERE `code` = "paypal_verify";
        ');

        return false;
    }
}
