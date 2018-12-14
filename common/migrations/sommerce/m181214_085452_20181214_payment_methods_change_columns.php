<?php

use yii\db\Migration;

/**
 * Class m181214_085452_20181214_payment_methods_change_columns
 */
class m181214_085452_20181214_payment_methods_change_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn(DB_STORES . '.payment_methods', 'method', 'method_name');
        $this->renameColumn(DB_STORES . '.payment_methods', 'options', 'settings_form');

        $this->dropColumn(DB_STORES . '.payment_methods', 'currencies');
        $this->dropColumn(DB_STORES . '.payment_methods', 'position');

        $this->alterColumn(DB_STORES . '.payment_methods', 'name', $this->string(255)->notNull());
        $this->alterColumn(DB_STORES . '.payment_methods', 'settings_form', $this->text()->null());

        $this->addColumn(DB_STORES . '.payment_methods', 'addfunds_form', $this->text()->null());
        $this->addColumn(DB_STORES . '.payment_methods', 'settings_form_description', $this->text()->null());
        $this->addColumn(DB_STORES . '.payment_methods', 'manual_callback_url', $this->smallInteger(1)->notNull()->defaultValue(0));
        $this->addColumn(DB_STORES . '.payment_methods', 'icon', $this->string(64));
        $this->addColumn(DB_STORES . '.payment_methods', 'created_at', $this->integer(11)->null());
        $this->addColumn(DB_STORES . '.payment_methods', 'updated_at', $this->integer(11)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn(DB_STORES . '.payment_methods', 'method_name', 'method');
        $this->renameColumn(DB_STORES . '.payment_methods', 'settings_form', 'options');

        $this->addColumn(DB_STORES . '.payment_methods', 'currencies', $this->string(3000)->null());
        $this->addColumn(DB_STORES . '.payment_methods', 'position', $this->tinyInteger(2));

        $this->alterColumn(DB_STORES . '.payment_methods', 'name', $this->string(300)->null());
        $this->alterColumn(DB_STORES . '.payment_methods', 'options', $this->text()->notNull());

        $this->dropColumn(DB_STORES . '.payment_methods', 'addfunds_form');
        $this->dropColumn(DB_STORES . '.payment_methods', 'settings_form_description');
        $this->dropColumn(DB_STORES . '.payment_methods', 'manual_callback_url');
        $this->dropColumn(DB_STORES . '.payment_methods', 'created_at');
        $this->dropColumn(DB_STORES . '.payment_methods', 'updated_at');
    }
}
