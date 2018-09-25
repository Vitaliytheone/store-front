<?php

use yii\db\Migration;

/**
 * Class m180925_130852_20180925_params_delete_index
 */
class m180925_130852_20180925_params_delete_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex('uniq_code', 'params');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->createIndex('uniq_code', 'params', 'code', true);
    }
}
