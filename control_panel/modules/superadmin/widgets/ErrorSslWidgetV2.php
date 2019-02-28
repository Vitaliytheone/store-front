<?php
namespace superadmin\widgets;

use common\models\sommerces\SslCert;
use yii\base\Widget;
use yii\bootstrap\Html;

/**
 * Class ErrorSslWidgetV2
 * @package superadmin\widgets
 */
class ErrorSslWidgetV2 extends Widget {

    /**
     * Run method
     * @return string|null
     */
    public function run()
    {
        $count = SslCert::find()
            ->select('COUNT(*)')
            ->andWhere([
                'status' => SslCert::STATUS_ERROR,
            ])
            ->scalar();

        if (!$count) {
            return null;
        }

        return Html::tag('span', $count, [
            'class' => 'badge badge-pill badge-danger'
        ]);
    }
}