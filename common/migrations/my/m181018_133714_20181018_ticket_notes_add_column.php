<?php

use yii\db\Migration;

/**
 * Class m181018_133714_20181018_ticket_notes_add_column
 */
class m181018_133714_20181018_ticket_notes_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('ticket_notes', 'created_by', $this->integer(11));
        $this->addColumn('ticket_notes', 'updated_by', $this->integer(11));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('ticket_notes', 'created_by');
        $this->dropColumn('ticket_notes', 'updated_by');
    }
}
