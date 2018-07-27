<?php

use yii\db\Migration;

/**
 * Class m180712_143032_update_stores_table
 */
class m180712_143032_update_stores_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(DB_STORES . '.stores', 'last_count', $this->integer(11)->defaultValue(0));
        $this->addColumn(DB_STORES . '.stores', 'current_count', $this->integer(11)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(DB_STORES . '.stores', 'last_count');
        $this->dropColumn(DB_STORES . '.stores', 'current_count');
    }
}
