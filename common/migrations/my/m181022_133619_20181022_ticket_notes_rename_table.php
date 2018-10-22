<?php

use yii\db\Migration;

/**
 * Class m181022_133619_20181022_ticket_notes_rename_table
 */
class m181022_133619_20181022_ticket_notes_rename_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameTable('ticket_notes', 'customers_note');

        $this->addForeignKey('fk-notes-customer_id', 'customers_note', 'customer_id', 'customers', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-notes-customer_id', 'customers_note');

        $this->renameTable('customers_note', 'ticket_notes');
    }
}
