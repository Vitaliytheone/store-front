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
     * @return string|void
     */
    public function run()
    {
        $count = Tickets::find()->andWhere([
            'cid' => Yii::$app->user->identity->id,
            'admin' => 1
        ])->count();

        if (!$count) {
            return;
        }

        return Html::tag('span', $count, [
            'class' => 'badge',
            'style' => 'background-color: #f0ad4e'
        ]);
    }
}