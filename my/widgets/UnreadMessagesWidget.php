<?php
namespace my\widgets;

use Yii;
use common\models\panels\Tickets;
use yii\base\Widget;
use yii\bootstrap\Html;

/**
 * Class UnreadMessagesWidget
 * @package my\widgets
 */
class UnreadMessagesWidget extends Widget {

    /**
     * Run method
     * @return string|null
     */
    public function run()
    {
        $count = Tickets::find()->andWhere([
            'customer_id' => Yii::$app->user->identity->id,
            'is_admin' => 1
        ])->count();

        if (!$count) {
            return null;
        }

        return Html::tag('span', $count, [
            'class' => 'sidebar-tooltip sidebar-tooltip__warning',
        ]);
    }
}