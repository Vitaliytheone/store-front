<?php

use yii\db\Migration;
use common\models\panels\Project;

/**
 * Class m181013_064652_20181013_panels_auto_orders_add_column
 */
class m181013_064652_20181013_panels_auto_orders_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $panels = Project::find()
            ->select('db')
            ->where('db IS NOT NULL')
            ->all();

        foreach ($panels as $panel) {
            $this->addColumn($panel['db'] . '.auto_orders', 'not_checked', $this->tinyInteger(1)->notNull()->defaultValue(0)->after('delay'));
        }

        $panelTemplateDb = Yii::$app->params['panelDefaultDatabase'];
        $this->addColumn($panelTemplateDb . '.auto_orders', 'not_checked', $this->tinyInteger(1)->notNull()->defaultValue(0)->after('delay'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $panels = Project::find()
            ->select('db')
            ->where('db IS NOT NULL')
            ->all();

        foreach ($panels as $panel) {
            $this->dropColumn($panel['db'] . '.auto_orders', 'not_checked');
        }
    }
}
