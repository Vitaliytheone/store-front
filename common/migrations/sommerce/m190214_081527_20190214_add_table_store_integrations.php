<?php

use yii\db\Migration;

/**
 * Class m190214_081527_20190214_add_table_store_integrations
 */
class m190214_081527_20190214_add_table_store_integrations extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('store_integrations', [
            'id' => $this->primaryKey()->notNull(),
            'integration_id' => $this->integer(),
            'store_id' => $this->integer(),
            'options' => $this->text(),
            'visibility' => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('1- активна, 0 - не активна'),
            'position' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->addForeignKey(
            'FK_integration_id',
            'store_integrations',
            'integration_id',
            'integrations',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK_store_id',
            'store_integrations',
            'store_id',
            'stores',
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
        $this->dropTable('store_integrations');
    }
}
