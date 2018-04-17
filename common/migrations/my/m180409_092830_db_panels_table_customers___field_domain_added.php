<?php

use yii\db\Migration;

/**
 * Class m180409_092830_db_panels_table_customers___field_domain_added
 */
class m180409_092830_db_panels_table_customers___field_domain_added extends Migration
{
    public function up()
    {
        $this->execute('
            USE `' . DB_PANELS . '`;
            ALTER TABLE `customers` ADD `buy_domain` TINYINT(1)  NULL  DEFAULT \'0\'  AFTER `stores`;
        ');
    }

    public function down()
    {
        $this->execute('
            USE `' . DB_PANELS . '`;
            ALTER TABLE `customers` DROP `buy_domain`;
        ');
    }
}
