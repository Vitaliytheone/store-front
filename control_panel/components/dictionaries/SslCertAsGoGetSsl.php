<?php

namespace control_panel\components\dictionaries;

use common\models\sommerces\SslCert;
use yii\helpers\ArrayHelper;

class SslCertAsGoGetSsl
{
    /**
     * Get ssl cert status by gogetssl service status name
     * @param $status
     * @return mixed
     */
    public static function getSslCertStatus($status)
    {
        $statuses = [
            'active' => SslCert::STATUS_ACTIVE,
            'pending' => SslCert::STATUS_PENDING,
            'canceled' => SslCert::STATUS_CANCELED,
            'payment needed' => SslCert::STATUS_PAYMENT_NEEDED,
            'processing' => SslCert::STATUS_PROCESSING,
            'incomplete' => SslCert::STATUS_INCOMPLETE
        ];

        return ArrayHelper::getValue($statuses, $status);
    }
}