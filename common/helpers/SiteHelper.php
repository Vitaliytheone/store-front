<?php
namespace common\helpers;

/**
 * Class SiteHelper
 * @package common\helpers
 */
class SiteHelper {

    /**
     * Get user ip
     */
    public static function ip()
    {
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
    }

    /**
     * Get host
     */
    public static function host()
    {
        return isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
    }

    /**
     * Get host url
     * @param int|bool $ssl
     * @return string
     */
    public static function hostUrl($ssl = false)
    {
        return ($ssl ? 'https' : 'http') . '://' . static::host();
    }

    /**
     * @param $url
     * @param bool $ssl
     * @return string
     */
    public static function pageUrl($url, $ssl = false) {
        return static::hostUrl($ssl) . '/' . trim($url, '/');
    }
}