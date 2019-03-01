<?php

use yii\db\Migration;

/**
 * Class m180420_064306_db_stores_table_store__ssl_added
 */
class m180420_064306_db_stores_table_store__ssl_added extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->execute("ALTER TABLE `stores` ADD `ssl` VARCHAR(1)  NULL  DEFAULT '0'  AFTER `subdomain`;");
    }

    public function down()
    {
        $this->execute("ALTER TABLE `stores` DROP `ssl`;");
    }
}
