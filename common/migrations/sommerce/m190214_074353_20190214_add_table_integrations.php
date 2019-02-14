<?php

use yii\db\Migration;

/**
 * Class m190214_074353_20180214_add_table_integrations
 */
class m190214_074353_20190214_add_table_integrations extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('integrations', [
            'id' => $this->primaryKey()->notNull(),
            'category' => $this->string(255)->notNull(),
            'code' => $this->string(255)->notNull()->unique(),
            'name' => $this->string(255),
            'widget_class' => $this->string(255),
            'settings_form' => $this->text(),
            'settings_description' => $this->text(),
            'visibility' => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('1- видема для всех, 0 - не видима для всех'),
            'position' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('integrations');
    }
}
