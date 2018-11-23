<?php

use yii\db\Migration;

/**
 * Class m181123_142105_20181023_paypal_fraud_accounts_add_columns
 */
class m181123_142105_20181023_paypal_fraud_accounts_add_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('paypal_fraud_accounts', 'lastname', $this->string(300)->defaultValue(null));
        $this->addColumn('paypal_fraud_accounts', 'firstname', $this->string(300)->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('paypal_fraud_accounts', 'lastname');
        $this->dropColumn('paypal_fraud_accounts', 'firstname');
    }
}
