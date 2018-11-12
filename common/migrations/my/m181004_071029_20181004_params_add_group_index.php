<?php

use yii\db\Migration;

/**
 * Class m181004_071029_20181004_params_add_group_index
 */
class m181004_071029_20181004_params_add_group_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('uniq_category_code', DB_PANELS . '.params', ['code', 'category'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('uniq_category_code', DB_PANELS . '.params');
    }
}
