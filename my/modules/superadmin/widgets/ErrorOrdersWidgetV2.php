<?php

namespace my\modules\superadmin\widgets;

use common\models\panels\Orders;

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
            ->from('orders')
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