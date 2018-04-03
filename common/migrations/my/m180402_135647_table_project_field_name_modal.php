<?php

use yii\db\Migration;

/**
 * Class m180402_135647_table_project_field_name_modal
 */
class m180402_135647_table_project_field_name_modal extends Migration
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
        echo "m180402_135647_table_project_field_name_modal cannot be reverted.\n";

        return false;
    }

    public function up()
    {
        $this->execute('
          USE `panels`;
          ALTER TABLE `project` ADD `name_modal` TINYINT(1) NOT NULL DEFAULT \'0\' AFTER `name_fields`;
        ');
    }

    public function down()
    {
        $this->execute('
            USE `panels`;
            ALTER TABLE `project` DROP `name_modal`;
        ');
    }
}
