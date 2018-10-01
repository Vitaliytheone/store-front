<?php

use yii\db\Migration;

/**
 * Class m181001_132701_20181001_additional_services_rename_column
 */
class m181001_132701_20181001_additional_services_rename_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('additional_services', 'res', 'panel_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('additional_services', 'panel_id', 'res');
    }
}
