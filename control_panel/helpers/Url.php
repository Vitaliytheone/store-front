<?php

namespace control_panel\helpers;

use Yii;

/**
 * Class Url
 * @package control_panel\helpers\Url
 */
class Url extends \yii\helpers\Url {

    /**
     * Make url
     * @param array|string $route
     * @param bool|string $scheme
     * @return string
     */
    public static function toRoute($route, $scheme = false)
    {
        if (Yii::$app->request->isConsoleRequest) {
            Yii::$app->urlManager->baseUrl = Yii::$app->params['myUrl'];

            return parent::toRoute($route, $scheme);
        }

        if (!empty(Yii::$app->controller->module->module)) {
            $module = Yii::$app->controller->module->id;
            if (!is_array($route)) {
                $route = [$route];
            }

            $route[0] = ltrim($route[0], '/');
            $route[0] = "/" . $module . "/" . $route[0];
            $route[0] = rtrim($route[0], "/");
        }

        return parent::toRoute($route, $scheme); // TODO: Change the autogenerated stub
    }
}