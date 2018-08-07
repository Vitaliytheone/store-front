<?php

use yii\db\Migration;

/**
 * Class m180731_073235_20180731_referral_earnings_update_comment
 */
class m180731_073235_20180731_referral_earnings_update_comment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addCommentOnColumn('referral_earnings', 'status', '1 - completed, 2 - rejected, 3 - cancel, 4 - debit');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropCommentFromColumn('referral_earnings', 'status');
    }
}
