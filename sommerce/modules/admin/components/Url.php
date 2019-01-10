<?php
namespace sommerce\modules\admin\components;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class Url
 * @package sommerce\modules\admin\components
 */
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

        return parent::toRoute($route, $scheme);
    }

    /**
     * Creates a URL by using the current route and the GET parameters
     * @param array $params
     * @param bool $scheme
     * @return string
     */
    public static function current(array $params = [], $scheme = false)
    {
        $currentParams = Yii::$app->getRequest()->getQueryParams();
        $currentParams[0] = '/' . Yii::$app->controller->getRoute();
        $route = ArrayHelper::merge($currentParams, $params);
        return parent::toRoute($route, $scheme);
    }
}