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
class UnreadMessagesWidget extends Widget {

    /**
     * Run method
     * @return string|void
     */
    public function run()
    {
        $count = (new Query())
            ->select('COUNT(*)')
            ->from(DB_SOMMERCES . '.tickets')
            ->andWhere([
                'is_user' => 1
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