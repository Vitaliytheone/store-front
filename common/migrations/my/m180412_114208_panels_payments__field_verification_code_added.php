<?php

use yii\db\Migration;

/**
 * Class m180412_114208_panels_payments__field_verification_code_added
 */
class m180412_114208_panels_payments__field_verification_code_added extends Migration
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
        echo "m180412_114208_panels_payments__field_verification_code_added cannot be reverted.\n";

        return false;
    }

    public function up()
    {
        $this->execute('
            USE `panels`;
            ALTER TABLE `payments` ADD `verification_code` VARCHAR(64)  NULL  DEFAULT NULL  AFTER `options`;
        ');
    }

    public function down()
    {
        $this->execute('
            USE `panels`;
            ALTER TABLE `payments` DROP `verification_code`;
        ');

        return false;
    }
}
