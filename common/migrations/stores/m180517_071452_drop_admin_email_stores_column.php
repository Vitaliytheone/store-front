<?php

use yii\db\Migration;

/**
 * Class m180517_071452_drop_admin_email_stores_column
 */
class m180517_071452_drop_admin_email_stores_column extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->execute("ALTER TABLE " . DB_STORES . ".`stores` DROP `admin_email`;");
    }

    public function down()
    {
        $this->execute("ALTER TABLE " . DB_STORES . ".`stores` ADD `admin_email` varchar(300) DEFAULT NULL AFTER `block_process`;");
    }
}
