<?php

use yii\db\Migration;

/**
 * Class m180821_144136_20092018_add_columns_additional_services
 */
class m180821_144136_20092018_add_columns_additional_services extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('additional_services', 'service_count', $this->integer(11)->defaultValue(0));
        $this->addColumn('additional_services', 'service_inuse_count', $this->integer(11)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('additional_services', 'service_count');
        $this->dropColumn('additional_services', 'service_inuse_count');
    }
}
