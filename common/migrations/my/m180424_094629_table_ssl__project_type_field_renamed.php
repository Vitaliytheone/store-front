<?php

use yii\db\Migration;

/**
 * Class m180424_094629_table_ssl__project_type_field_renamed
 */
class m180424_094629_table_ssl__project_type_field_renamed extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->execute('
            USE `' . DB_PANELS . '`;
            ALTER TABLE `ssl_cert` CHANGE `ptype` `project_type` TINYINT(1)  NULL  DEFAULT NULL  COMMENT \'1 - Panel, 2 - Store\';
        ');

        $this->execute('
            USE `' . DB_PANELS . '`;
            ALTER TABLE `ssl_validation` DROP `ptype`;
        ');
    }

    public function down()
    {
        $this->execute('
            USE `' . DB_PANELS . '`;
            ALTER TABLE `ssl_cert` CHANGE `project_type` `ptype` TINYINT(1)  NULL  DEFAULT NULL  COMMENT \'1 - Panel, 2 - Store\';
        ');

        $this->execute('
            USE `' . DB_PANELS . '`;
            ALTER TABLE `ssl_validation` ADD `ptype` VARCHAR(1)  NULL  DEFAULT NULL  COMMENT \'1-Panel, 2-Sommerce\'  AFTER `id`;
        ');
    }
}
