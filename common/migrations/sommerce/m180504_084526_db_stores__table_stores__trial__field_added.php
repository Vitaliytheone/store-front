<?php

use yii\db\Migration;

/**
 * Class m180504_084526_db_stores__table_stores__trial__field_added
 */
class m180504_084526_db_stores__table_stores__trial__field_added extends Migration
{
    public function up()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            ALTER TABLE `stores` ADD `trial` TINYINT(1)  NOT NULL  DEFAULT \'0\'  AFTER `db_name`;
        ');

        $this->execute('
            USE `' . DB_STORES . '`;
            UPDATE `stores` SET `trial` = \'1\' WHERE `expired` - `created_at` <= 14 * 24 * 60 * 60;
        ');
    }

    public function down()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            ALTER TABLE `stores` DROP `trial`;
        ');
    }
}
