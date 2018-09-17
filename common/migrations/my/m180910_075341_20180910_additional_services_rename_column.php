<?php

use yii\db\Migration;

/**
 * Class m180910_075341_20180910_additional_services_rename_column
 */
class m180910_075341_20180910_additional_services_rename_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('additional_services', 'sc', 'start_count');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('additional_services', 'start_count', 'sc');
    }
}
