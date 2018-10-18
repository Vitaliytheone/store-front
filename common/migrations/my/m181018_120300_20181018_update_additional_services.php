<?php

use yii\db\Migration;

/**
 * Class m181018_120300_20181018_update_additional_services
 */
class m181018_120300_20181018_update_additional_services extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropPrimaryKey('PRIMARY', 'additional_services');

        $this->addPrimaryKey('', 'additional_services', 'id');
        $this->createIndex('res', 'additional_services', 'res');
        $this->alterColumn('additional_services', 'id', $this->integer(11).' NOT NULL AUTO_INCREMENT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropPrimaryKey('PRIMARY', 'additional_services');
        $this->dropIndex('res', 'additional_services');

        $this->addPrimaryKey('PRIMARY', 'additional_services', 'res');
    }
}
