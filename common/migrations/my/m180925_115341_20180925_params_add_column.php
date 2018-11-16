<?php

use yii\db\Migration;

/**
 * Class m180925_115341_20180925_params_add_column
 */
class m180925_115341_20180925_params_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('params', 'position', $this->integer(11));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('params', 'position');
    }
}
