<?php

use yii\db\Migration;

/**
 * Class m180830_071828_20180830_stores_add_index
 */
class m180830_071828_20180830_stores_add_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('idx_store_id', DB_STORES.'store_domains', 'store_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_store_id', DB_STORES.'store_domains');
    }
}
