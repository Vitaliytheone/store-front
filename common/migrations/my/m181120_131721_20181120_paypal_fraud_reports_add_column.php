<?php

use yii\db\Migration;

/**
 * Class m181120_131721_20181120_paypal_fraud_reports_add_column
 */
class m181120_131721_20181120_paypal_fraud_reports_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('paypal_fraud_reports', 'transaction_details', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('paypal_fraud_reports', 'transaction_details');
    }
}
