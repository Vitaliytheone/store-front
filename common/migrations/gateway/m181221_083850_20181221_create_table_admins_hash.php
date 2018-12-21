<?php

use yii\db\Migration;

/**
 * Class m181221_083850_20181221_create_table_admins_hash
 */
class m181221_083850_20181221_create_table_admins_hash extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(DB_GATEWAYS . '.admins_hash', [
            'id' => $this->primaryKey(),
            'admin_id' => $this->integer(11)->notNull(),
            'hash' => $this->string(64)->notNull(),
            'ip' => $this->string(255)->notNull(),
            'super_user' => $this->smallInteger(1)->notNull()->defaultValue(0)->comment('0 - admin, 1 - superadmin'),
            'updated_at' => $this->integer(11)->notNull()->defaultValue(0),
            'created_at' => $this->integer(11)->notNull()->defaultValue(0)
        ]);

        $this->createIndex('__admin_id__hash', DB_GATEWAYS . '.admins_hash', ['admin_id', 'hash']);
        $this->createIndex('idx_hash', DB_GATEWAYS . '.admins_hash', 'hash');
        $this->createIndex('idx_admin_id', DB_GATEWAYS . '.admins_hash', 'admin_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(DB_GATEWAYS . '.admins_hash');
    }
}
