<?php

use yii\db\Migration;

/**
 * Class m180430_130604_table_stores__fields__custom_header__custom_footer_added
 */
class m180430_130604_table_stores__fields__custom_header__custom_footer_added extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            ALTER TABLE `stores` ADD `custom_header` TEXT  NULL  AFTER `admin_email`;
            ALTER TABLE `stores` ADD `custom_footer` TEXT  NULL  AFTER `custom_header`;
        ');
    }

    public function down()
    {
        $this->execute('
            USE `' . DB_STORES . '`;
            ALTER TABLE `stores` DROP `custom_footer`;
            ALTER TABLE `stores` DROP `custom_header`;
        ');
    }
}
