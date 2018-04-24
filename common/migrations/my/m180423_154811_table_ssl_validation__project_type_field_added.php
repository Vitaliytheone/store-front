<?php

use yii\db\Migration;

/**
 * Class m180423_154811_table_ssl_validation__project_type_field_added
 */
class m180423_154811_table_ssl_validation__project_type_field_added extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->execute('
            USE `' . DB_PANELS . '`;
            ALTER TABLE `ssl_validation` ADD `ptype` VARCHAR(1)  NULL  DEFAULT NULL  COMMENT \'1-Panel, 2-Sommerce\'  AFTER `id`;
            ALTER TABLE `ssl_validation` DROP FOREIGN KEY `fk_ssl_validation__project`;
            UPDATE `ssl_validation` SET `ptype` = \'1\';
        ');
    }

    public function down()
    {
        $this->execute('
            USE `' . DB_PANELS . '`;
            ALTER TABLE `ssl_validation` DROP `ptype`;
            ALTER TABLE `ssl_validation` ADD FOREIGN KEY (`fk_ssl_validation__project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
        ');
    }
}