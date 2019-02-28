<?php

namespace control_panel\components\ddos;

use Yii;
use common\helpers\CurlHelper;
use yii\helpers\Json;

/**
 * Class Ddos
 * @package control_panel\components\ddos
 */
class Ddos
{
    /**
     * Add domain
     * @param array $options
     * @param mixed $result
     * @return bool
     */
    public static function add($options, &$result)
    {
        Yii::info(Json::encode($options), 'ssl_order_status');

        $result = CurlHelper::request(Yii::$app->params['ddosGuardUrl'], Json::encode($options));

        if ($result && 'ok' == strtolower($result)) {
            return true;
        }

        return false;
    }
}