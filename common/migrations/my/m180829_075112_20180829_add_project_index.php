<?php

use yii\db\Migration;

/**
 * Class m180829_075112_20180829_add_project_index
 */
class m180829_075112_20180829_add_project_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('idx_cid', 'project', 'cid');
        $this->addForeignKey('fk_project__customers', 'project', 'cid', 'customers', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_cid', 'project');
        $this->dropForeignKey('fk_project__customers', 'project');
    }
}
