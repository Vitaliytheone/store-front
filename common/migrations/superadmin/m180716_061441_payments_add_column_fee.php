<?php

use yii\db\Migration;

/**
 * Class m180716_061441_payments_add_column_fee
 */
class m180716_061441_payments_add_column_fee extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('payments', 'fee', $this->decimal(10, 5)->defaultValue(null)->after('amount'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('payments', 'fee');
    }
}
