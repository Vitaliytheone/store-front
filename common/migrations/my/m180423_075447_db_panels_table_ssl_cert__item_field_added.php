<?php

use yii\db\Migration;

/**
 * Class m180423_075447_db_panels_table_ssl_cert__item_field_added
 */
class m180423_075447_db_panels_table_ssl_cert__item_field_added extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->execute('
            USE `' . DB_PANELS . '`;
            ALTER TABLE `ssl_cert` ADD `ptype` TINYINT(1) NULL COMMENT \'1 - Panel, 2 - Store\' AFTER `cid`;
            UPDATE `ssl_cert` SET `ptype` = \'1\';
        ');
    }

    public function down()
    {
        $this->execute('
            USE `' . DB_PANELS . '`;
            ALTER TABLE `ssl_cert` DROP `ptype`;
        ');
    }
}
