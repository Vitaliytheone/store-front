<?php
namespace superadmin\widgets;

use Yii;
use common\models\sommerces\Tickets;
use yii\base\Widget;
use yii\bootstrap\Html;
use yii\db\Query;

/**
 * Class UnreadMessagesWidget
 * @package control_panel\widgets
 */
class UnreadMessagesWidgetV2 extends Widget {

    /**
     * Run method
     * @return string|void
     */
    public function run()
    {
        $count = (new Query())
            ->select('COUNT(*)')
            ->from('tickets')
            ->andWhere([
                'is_user' => 1
            ])
            ->scalar();

        if (!$count) {
            return;
        }

        return Html::tag('span', $count, [
            'class' => 'badge badge-pill badge-primary',
        ]);
    }
}