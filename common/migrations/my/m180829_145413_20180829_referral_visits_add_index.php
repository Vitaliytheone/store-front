<?php

use yii\db\Migration;

/**
 * Class m180829_145413_20180829_referral_visits_add_index
 */
class m180829_145413_20180829_referral_visits_add_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('idx_customer_id', 'referral_visits', 'customer_id');
        $this->addForeignKey('fk_referral_visits_customers', 'referral_visits', 'customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_customer_id', 'referral_visits');
        $this->dropForeignKey('fk_referral_visits_customers', 'referral_visits');
    }

}
