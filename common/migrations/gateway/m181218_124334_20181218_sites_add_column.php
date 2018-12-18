<?php

use yii\db\Migration;

/**
 * Class m181218_124334_20181218_sites_add_column
 */
class m181218_124334_20181218_sites_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(DB_GATEWAYS . '.sites', 'status', $this->integer()->notNull()->after('customer_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(DB_GATEWAYS . '.sites', 'status');
    }
}
