<?php

use yii\db\Migration;

/**
 * Class m190311_075935_20190311_project_add_column
 */
class m190311_075935_20190311_project_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            USE " . DB_PANELS . ";
    
            ALTER TABLE `project` 
              ADD `languages_for_child_panel` TEXT NULL DEFAULT NULL;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(DB_PANELS . '.project', 'languages_for_child_panel');
    }
}
