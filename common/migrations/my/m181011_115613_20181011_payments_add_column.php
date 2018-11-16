<?php

use yii\db\Migration;

/**
 * Class m181011_115613_20181011_payments_add_column
 */
class m181011_115613_20181011_payments_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(DB_PANELS . '.payments', 'payment_method', $this->string(64)->after('type'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(DB_PANELS . '.payments', 'payment_method');
    }
}
