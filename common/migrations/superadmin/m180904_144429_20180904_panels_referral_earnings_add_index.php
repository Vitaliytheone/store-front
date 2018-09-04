<?php

use yii\db\Migration;

/**
 * Class m180904_144429_20180904_panels_referral_earnings_add_index
 */
class m180904_144429_20180904_panels_referral_earnings_add_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('idx_status', 'referral_earnings', 'status');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_status', 'referral_earnings');
    }
}
