<?php

use yii\db\Migration;

/**
 * Handles adding subdomain to table `stores`.
 */
class m180328_143445_add_subdomain_column_to_stores_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `stores` ADD `subdomain` smallint(1) NULL DEFAULT '0' AFTER `domain`;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE `stores` DROP `subdomain`;");
    }
}
