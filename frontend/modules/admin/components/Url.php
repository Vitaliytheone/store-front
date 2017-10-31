<?php
namespace frontend\modules\admin\components;

use Yii;

class Url extends \yii\helpers\Url {

    /**
     * Make url
     * @param array|string $route
     * @param bool $scheme
     * @return string
     */
    public static function toRoute($route, $scheme = false)
    {
        if (!empty(Yii::$app->controller->module)) {
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