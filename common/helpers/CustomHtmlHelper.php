<?php

namespace common\helpers;

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
}