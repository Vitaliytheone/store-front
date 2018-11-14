<?php

use yii\db\Migration;

/**
 * Class m181004_070018_20181004_params_add_column
 */
class m181004_070018_20181004_params_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(DB_PANELS . '.params', 'category', $this->string(64)->after('id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(DB_PANELS . '.params', 'category');
    }
}
