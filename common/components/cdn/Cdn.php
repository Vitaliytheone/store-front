<?php

namespace common\components\cdn;

use Yii;
use yii\base\Exception;
use yii\base\UnknownClassException;
use yii\helpers\ArrayHelper;

class Cdn
{
    public static function getCdn($cdnProvider = null)
    {
        // Get Cdn providers configurations
        $cdnConfigs = ArrayHelper::getValue(Yii::$app->params, "cdn", null);
        $config = null;

        // Get from cdn-providers list by provider name if it passed to constructor
        if ($cdnProvider) {
            $config = ArrayHelper::getValue($cdnConfigs, "$cdnProvider", null);
        }

        // Get first-single from cdn-providers list
        if (!$cdnProvider && is_array($cdnConfigs) && count($cdnConfigs) === 1) {
            $config = array_values($cdnConfigs)[0];
        }

        // Get by first active provider from cdn-providers list
        if (!$cdnProvider && is_array($cdnConfigs) && count($cdnConfigs) > 1) {
            foreach ($cdnConfigs as $cdnConfig) {
                if (array_key_exists('active', $cdnConfig)) {
                    $config = $cdnConfig;
                    break;
                }
            }
        }

        if (!$config) {
            throw new Exception(BaseCdn::MESSAGE_BAD_CONFIG);
        }

        $className = ArrayHelper::getValue($config, 'class_name', null);
        $classPath = '\common\components\cdn\providers\\' . $className;

        if (!class_exists($classPath)) {
            throw new UnknownClassException($classPath . " does not exist.");
        }

        return new $classPath($config);
    }
}