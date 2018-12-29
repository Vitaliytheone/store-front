<?php

use yii\db\Migration;

/**
 * Class m181224_065321_update_payments_log_table
 */
class m181224_065321_update_payments_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            USE `" . DB_GATEWAY . "`;

            ALTER TABLE `payments_log`
                CHANGE `response` `response` text NULL AFTER `payment_id`,
                ADD `result` text NULL AFTER `response`;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
            USE `' . DB_GATEWAY . '`;
            
            ALTER TABLE `payments_log`
                CHANGE `response` `response` text NOT NULL AFTER `payment_id`,
                DROP `result`;
        ');
    }
}
