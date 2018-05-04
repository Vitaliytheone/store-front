<?php

use yii\db\Migration;

/**
 * Class m180504_111402_db_panels_table_orders__hide__field_added
 */
class m180504_111402_db_panels_table_orders__hide__field_added extends Migration
{
    public function up()
    {
        $this->execute('
            USE `' . DB_PANELS . '`;
            ALTER TABLE `orders` ADD `hide` TINYINT(1)  NULL  DEFAULT \'0\'  AFTER `status`;
        ');
    }

    public function down()
    {
        $this->execute('
            USE `' . DB_PANELS . '`;
            ALTER TABLE `orders` DROP `hide`;
        ');
    }
}
