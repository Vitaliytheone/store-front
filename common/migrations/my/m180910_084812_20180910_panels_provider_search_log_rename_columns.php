<?php

use yii\db\Migration;

/**
 * Class m180910_084812_20180910_panels_provider_search_log_rename_columns
 */
class m180910_084812_20180910_panels_provider_search_log_rename_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('provider_search_log', 'uid', 'admin_id');
        $this->renameColumn('provider_search_log', 'pid', 'panel_id');
        $this->renameColumn('provider_search_log', 'date', 'created_at');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('provider_search_log', 'admin_id', 'uid');
        $this->renameColumn('provider_search_log', 'panel_id', 'pid');
        $this->renameColumn('provider_search_log', 'created_at', 'date');
    }
}
