<?php

namespace common\helpers;

use yii\base\Exception;
use yii\helpers\Html;

/**
 * Class CustomHtmlHelper
 * @package common\helpers
 */
class CustomHtmlHelper extends Html
{
    /**
     * Return escaped and formatted provider/payment system response data
     * @param string $data can be string or json string or array
     * @return string | array
     */
    public static function responseFormatter($data)
    {
        $jsonDecodedResult = json_decode($data,true);

        if(json_last_error()){
            $response = $data;
        } else {
             $response = $jsonDecodedResult;
        }

        $response = print_r($response, 1);

        return static::encode($response);
    }

    /**
     * Recursively encoding array keys and values
     * @param array $array
     * @throws Exception
     */
    public static function arrayEncoder(array &$array)
    {
        if (!is_array($array)) {
            throw new Exception('Array expected!');
        }

        foreach(array_keys($array) as $key) {
            $value = &$array[$key];
            unset($array[$key]);

            if (is_array($value)) {
                static::arrayEncoder($value);
            } else {
                $value = Html::encode($value);
            };

            $array[Html::encode($key)] = $value;
            
            unset($value);
        }
    }
}