<?php

use yii\db\Migration;

/**
 * Class m180831_084717_20180831_stores_store_domains_add_index
 */
class m180831_084717_20180831_stores_store_domains_add_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('idx_type', 'store_domains', 'type');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_type', 'store_domains');
    }
}
