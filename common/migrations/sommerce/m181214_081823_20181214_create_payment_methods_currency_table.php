<?php

use yii\db\Migration;

/**
 * Class m181214_081823_20181214_create_payment_methods_currency_table
 */
class m181214_081823_20181214_create_payment_methods_currency_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('payment_methods_currency', [
            'id' => $this->primaryKey(11)->unsigned(),
            'method_id' => $this->integer(11)->unsigned(),
            'currency' => $this->char(3),
            'position' => $this->integer(11),
            'settings_form' => $this->text()->null(),
            'settings_form_description' => $this->text()->null(),
            'hidden' => $this->smallInteger(1)->defaultValue(0),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ]);

        $this->createIndex(
            'idx_method_id',
            'payment_methods_currency',
            'method_id'
        );

        $this->addForeignKey(
            'fk_method_id',
            'payment_methods_currency',
            'method_id',
            'payment_methods',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('payment_methods_currency');
    }
}
