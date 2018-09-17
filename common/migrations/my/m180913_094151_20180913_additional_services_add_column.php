<?php

use yii\db\Migration;

/**
 * Class m180913_094151_20180913_additional_services_add_column
 */
class m180913_094151_20180913_additional_services_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('additional_services', 'provider_id', $this->integer(11)->after('res'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('additional_services', 'provider_id');
    }
}
