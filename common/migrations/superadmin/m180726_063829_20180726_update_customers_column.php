<?php

use yii\db\Migration;

/**
 * Class m180726_063829_20180726_update_customers_column
 */
class m180726_063829_20180726_update_customers_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('customers', 'unpaid_earnings');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('customers', 'unpaid_earnings', $this->decimal(20, 5)->defaultValue(null)->after('auth_token'));
    }
}
