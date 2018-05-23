<?php
namespace my\widgets;

use common\models\panels\Invoices;
use Yii;
use yii\base\Widget;
use yii\bootstrap\Html;

/**
 * Class UnpaidInvoicesWidget
 * @package my\widgets
 */
class UnpaidInvoicesWidget extends Widget
{

    /**
     * Run method
     * @return string|null
     */
    public function run()
    {
        $count = Invoices::find()->andWhere([
            'cid' => Yii::$app->user->identity->id,
            'status' => Invoices::STATUS_UNPAID,
        ])->count();

        if (!$count) {
            return null;
        }

        return Html::tag('span', $count, [
            'class' => 'sidebar-tooltip sidebar-tooltip__danger',
        ]);
    }
}