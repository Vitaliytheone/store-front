<?php

use yii\db\Migration;

/**
 * Class m181023_072657_20181023_customers_note_add_foreign_keys
 */
class m181023_072657_20181023_customers_note_add_foreign_keys extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addForeignKey('fk_notes_created_by', 'customers_note', 'created_by', 'super_admin', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_notes_updated_by', 'customers_note', 'updated_by', 'super_admin', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_notes_created_by', 'customers_note');
        $this->dropForeignKey('fk_notes_updated_by', 'customers_note');
    }
}
