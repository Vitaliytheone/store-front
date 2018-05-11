<?php
namespace my\modules\superadmin\widgets;

use common\models\panels\SslCert;
use yii\base\Widget;
use yii\bootstrap\Html;

/**
 * Class ErrorSslWidget
 * @package my\modules\superadmin\widgets
 */
class ErrorSslWidget extends Widget {

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
            'class' => 'badge',
            'style' => 'background-color: #f0ad4e'
        ]);
    }
}