<?php
namespace store\helpers;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class ConfigHelper
 * @package store\helpers
 */
class ConfigHelper {

    /**
     * Return config — params array
     * @return array
     */
    public static function  getParams()
    {
        $params = array_merge(
            require(__DIR__ . '/../../common/config/params.php'),
            file_exists(__DIR__ . '/../../common/config/params-local.php') ? require(__DIR__ . '/../../common/config/params-local.php') : [],
            require(__DIR__ . '/../../store/config/params.php'),
            file_exists(__DIR__ . '/../../store/params-local.php') ? require(__DIR__ . '/../../store/params-local.php') : []
        );

        return $params;
    }

    /**
     * Return requested param value if set or null
     * @param $paramName
     * @return mixed
     */
    public static function getParam($paramName)
    {
        return ArrayHelper::getValue(static::getParams(), $paramName,null);
    }

    /**
     * Return store currencies key-value list
     * @return array
     */
    public static function getCurrenciesList()
    {
        $currencies = Yii::$app->params['currencies'];

        array_walk($currencies, function(&$value, $key){
            $value = $value['name'] . " ($key)";
        });

        return $currencies;
    }
}