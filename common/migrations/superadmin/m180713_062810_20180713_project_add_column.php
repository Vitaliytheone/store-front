<?php

use yii\db\Migration;

/**
 * Class m180713_062810_20180713_project_add_column
 */
class m180713_062810_20180713_project_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('project', 'currency_code', 'char(3) AFTER currency_format');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropColumn('project', 'currency_code');
    }
}
