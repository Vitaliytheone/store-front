<?php

namespace superadmin\widgets;

use common\models\sommerces\Orders;
use yii\bootstrap\Html;
use yii\db\Query;
use yii\base\Widget;

class ErrorOrdersWidgetV2 extends Widget
{
    /**
     * Run method
     * @return string|void
     */
    public function run()
    {
        $count = (new Query())
            ->select('COUNT(*)')
            ->from(DB_SOMMERCES . '.orders')
            ->andWhere([
                'status' => Orders::STATUS_ERROR
            ])
            ->scalar();

        if (!$count) {
            return;
        }

        return Html::tag('span', $count, [
            'class' => 'badge badge-pill badge-danger'
        ]);
    }
}