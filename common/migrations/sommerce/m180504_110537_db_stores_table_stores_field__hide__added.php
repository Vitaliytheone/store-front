<?php

use yii\db\Migration;

/**
 * Class m180504_110537_db_stores_table_stores_field__hide__added
 */
class m180504_110537_db_stores_table_stores_field__hide__added extends Migration
{
    public function up()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            ALTER TABLE `stores` ADD `hide` TINYINT(1)  NULL  DEFAULT \'0\'  AFTER `status`;
        ');
    }

    public function down()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            ALTER TABLE `stores` DROP `hide`;
        ');

    }
}
