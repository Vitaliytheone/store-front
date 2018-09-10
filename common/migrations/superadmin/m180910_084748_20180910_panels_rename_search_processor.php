<?php

use yii\db\Migration;

/**
 * Class m180910_084748_20180910_panels_rename_search_processor
 */
class m180910_084748_20180910_panels_rename_search_processor extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameTable('search_processor', 'provider_search_log');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameTable('provider_search_log', 'search_processor');
    }
}
