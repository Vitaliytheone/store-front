<?php

use yii\db\Migration;

/**
 * Class m180904_132046_20180904_store_template_add_column
 */
class m180904_132046_20180904_store_template_pages_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(Yii::$app->params['storeDefaultDatabase'] . '.pages', 'is_default', $this->integer(0)->defaultValue(0));

        $this->update(Yii::$app->params['storeDefaultDatabase'] . '.pages', ['is_default' => 1]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(Yii::$app->params['storeDefaultDatabase'] . '.pages', 'is_default');
    }
}
