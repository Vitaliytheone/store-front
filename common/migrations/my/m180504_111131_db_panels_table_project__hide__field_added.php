<?php

use yii\db\Migration;

/**
 * Class m180504_111131_db_panels_table_project__hide__field_added
 */
class m180504_111131_db_panels_table_project__hide__field_added extends Migration
{
    public function up()
    {
        $this->execute('
            USE `' . DB_PANELS . '`;
            ALTER TABLE `project` ADD `hide` TINYINT(1)  NULL  DEFAULT \'0\'  AFTER `act`;
        ');
    }

    public function down()
    {
        $this->execute('
            USE `' . DB_PANELS . '`;
            ALTER TABLE `project` DROP `hide`;
        ');

    }
}
