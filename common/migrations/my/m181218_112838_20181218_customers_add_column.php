<?php

use yii\db\Migration;

/**
 * Class m181218_112838_20181218_customers_add_column
 */
class m181218_112838_20181218_customers_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(DB_PANELS . '.customers', 'gateway', $this->smallInteger(1)->defaultValue(0)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(DB_PANELS . '.customers', 'gateway');
    }
}
