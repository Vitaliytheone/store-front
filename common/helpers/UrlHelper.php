<?php
namespace common\helpers;

use yii\helpers\Url;

/**
 * Class UrlHelper
 * @package common\helpers
 */
class UrlHelper extends Url {

    /**
     * @return bool
     */
    public static function isOurCall()
    {
        $refererDomain = !empty($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) : null;
        $currentDomain = $_SERVER['HTTP_HOST'];

        if (empty($refererDomain) || mb_strtolower($refererDomain) !== mb_strtolower($currentDomain)) {
            return false;
        }

        return true;
    }
}