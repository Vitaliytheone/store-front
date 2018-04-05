<?php

use yii\db\Migration;

/**
 * Handles adding updated_at to table `store_domains`.
 */
class m180330_122943_add_updated_at_column_to_store_domains_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `store_domains` ADD `updated_at` int(11) NOT NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE `store_domains` DROP `updated_at`;");
    }
}
