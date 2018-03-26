<?php

use yii\db\Migration;

/**
 * Class m180308_081952_167_add_rules_column_to_project_admin_table
 */
class m180308_081952_167_add_rules_column_to_project_admin_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `project_admin` ADD `rules` TEXT NOT NULL AFTER `update_at`;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE `project_admin` DROP `rules`;");
    }
}
