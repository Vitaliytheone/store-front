<?php

use yii\db\Migration;

/**
 * Class m181017_130941_20181017_panels_create_table_ticket_notes
 */
class m181017_130941_20181017_panels_create_table_ticket_notes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('ticket_notes', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer(11)->notNull(),
            'note' => $this->string(1000)->notNull(),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('ticket_notes');
    }
}
