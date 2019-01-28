<?php

namespace common\helpers;

/**
 * Class Request
 * @package common\helpers
 */
class Request
{
    /**
     * @param $url
     * @return bool|string
     */
    public static function getContents($url)
    {
        if (!empty(PROXY_CONFIG['main']['ip'])) {
            $aContext = array(
                'http' => array(
                    'proxy' => PROXY_CONFIG['main']['ip'] . ':' . PROXY_CONFIG['main']['port'],
                    'request_fulluri' => true,
                ),
            );
            $cxContext = stream_context_create($aContext);
            return @file_get_contents($url, false, $cxContext);
        }

        \Yii::debug($url, '$url'); //todo del
        return @file_get_contents($url);
    }
}