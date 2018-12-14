<?php

use yii\db\Migration;

/**
 * Class m181214_094857_20181214_store_payment_methods_change_columns
 */
class m181214_094857_20181214_store_payment_methods_change_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn(DB_STORES . '.store_payment_methods', 'details', 'options');
        $this->renameColumn(DB_STORES . '.store_payment_methods', 'active', 'visibility');

        $this->alterColumn(DB_STORES . '.store_payment_methods', 'id', $this->integer(11)->unsigned());
        $this->alterColumn(DB_STORES . '.store_payment_methods', 'store_id', $this->integer(11)->notNull());
        $this->alterColumn(DB_STORES . '.store_payment_methods', 'options', $this->text()->null());
        $this->alterColumn(DB_STORES . '.store_payment_methods', 'visibility', $this->tinyInteger(1)->notNull()->defaultValue(1));

        $this->dropColumn(DB_STORES . '.store_payment_methods', 'method');

        $this->addColumn(DB_STORES . '.store_payment_methods', 'method_id', $this->integer(11)->unsigned());
        $this->addColumn(DB_STORES . '.store_payment_methods', 'currency_id', $this->integer(11)->unsigned()->null());
        $this->addColumn(DB_STORES . '.store_payment_methods', 'name', $this->string(255));
        $this->addColumn(DB_STORES . '.store_payment_methods', 'position', $this->integer(11));
        $this->addColumn(DB_STORES . '.store_payment_methods', 'created_at', $this->integer(11)->null());
        $this->addColumn(DB_STORES . '.store_payment_methods', 'updated_at', $this->integer(11)->null());

        $this->dropIndex('fk_store_id_method_idx', DB_STORES . '.store_payment_methods');

        $this->createIndex('idx_store_id', DB_STORES . '.store_payment_methods', 'store_id');
        $this->createIndex('idx_method_id', DB_STORES . '.store_payment_methods', 'method_id');
        $this->createIndex('idx_currency_id', DB_STORES . '.store_payment_methods', 'currency_id');

        $this->addForeignKey(
            'fk_method_id_to_payment_methods',
            'store_payment_methods',
            'method_id',
            'payment_methods',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_currency_id_to_methods_currency',
            'store_payment_methods',
            'currency_id',
            'payment_methods_currency',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_store_id_to_stores',
            'store_payment_methods',
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
        $this->dropIndex('idx_store_id', DB_STORES . '.store_payment_methods');
        $this->dropIndex('idx_method_id', DB_STORES . '.store_payment_methods');
        $this->dropIndex('idx_currency_id', DB_STORES . '.store_payment_methods');

        $this->renameColumn(DB_STORES . '.store_payment_methods', 'options', 'details');
        $this->renameColumn(DB_STORES . '.store_payment_methods', 'visibility', 'active');

        $this->alterColumn(DB_STORES . '.store_payment_methods', 'id', $this->integer(11));
        $this->alterColumn(DB_STORES . '.store_payment_methods', 'store_id', $this->integer(11)->null());
        $this->alterColumn(DB_STORES . '.store_payment_methods', 'details', $this->text()->null());
        $this->alterColumn(DB_STORES . '.store_payment_methods', 'active', $this->tinyInteger(1)->null());

        $this->addColumn(DB_STORES . '.store_payment_methods', 'method', $this->string(255)->null());

        $this->dropColumn(DB_STORES . '.store_payment_methods', 'method_id');
        $this->dropColumn(DB_STORES . '.store_payment_methods', 'currency_id');
        $this->dropColumn(DB_STORES . '.store_payment_methods', 'name');
        $this->dropColumn(DB_STORES . '.store_payment_methods', 'position');
        $this->dropColumn(DB_STORES . '.store_payment_methods', 'created_at');
        $this->dropColumn(DB_STORES . '.store_payment_methods', 'updated_at');

        $this->createIndex('fk_store_id_method_idx', DB_STORES . '.store_payment_methods', 'store_id');
    }
}
