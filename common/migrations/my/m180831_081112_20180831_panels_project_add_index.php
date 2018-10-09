<?php

use yii\db\Migration;

/**
 * Class m180831_081112_20180831_panels_project_add_index
 */
class m180831_081112_20180831_panels_project_add_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('idx_child_panel', 'project', 'child_panel');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_child_panel', 'project');
    }
}
