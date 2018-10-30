<?php
namespace superadmin\widgets;

use common\models\panels\Orders;
use Yii;
use yii\base\Widget;
use yii\bootstrap\Html;
use yii\db\Query;

/**
 * Class ErrorOrdersWidget
 * @package my\widgets
 */
class ErrorOrdersWidget extends Widget {

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
            'class' => 'badge',
            'style' => 'background-color: #f0ad4e'
        ]);
    }
}