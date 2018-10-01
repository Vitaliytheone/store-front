<?php

use yii\db\Migration;

/**
 * Class m181001_124622_20181001_user_services_rename_columns
 */
class m181001_124622_20181001_user_services_rename_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('user_services', 'aid', 'provider_id');
        $this->renameColumn('user_services', 'pid', 'panel_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('user_services', 'provider_id', 'aid');
        $this->renameColumn('user_services', 'panel_id', 'pid');
    }
}
