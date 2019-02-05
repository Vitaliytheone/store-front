<?php
namespace gateway\helpers;

use common\models\gateway\Files;
use common\models\gateway\Pages;
use common\models\gateways\SitePaymentMethods;
use Yii;
use common\models\gateways\Sites;
use yii\helpers\ArrayHelper;

/**
 * Class RouteHelper
 * @package gateway\helpers
 */
class RouteHelper {

    /**
     * Get routes
     * @return array
     */
    public static function getRoutes()
    {
        $urls = [];

        $urls = ArrayHelper::merge($urls, static::getPaymentRules());
        $urls = ArrayHelper::merge($urls, static::getPagesRules());

        array_multisort(array_map('strlen', array_keys($urls)), SORT_DESC, $urls);

        return $urls;
    }

    /**
     * Get payments urls
     * @return array
     */
    public static function getPaymentRules()
    {
        /**
         * @var $gateway Sites
         */
        $gateway = Yii::$app->gateway->getInstance();

        $urls = [];

        $paymentMethods = SitePaymentMethods::find()
            ->joinWith(['method'])
            ->andWhere([
                'site_id' => $gateway->id
            ])
            ->all();

        foreach ($paymentMethods as $method) {
            $method = $method->method;
            $url = $method->url;

            if (empty($url)) {
                continue;
            }
            $urls[$url] = [
                'rule' => "/^\/?{$url}(?:\/(?<id>[\d]+))?.*?$/i",
                'match' => [
                    'id'
                ],
                'options' => [
                    'method' => $method->class_name,
                ],
                'url' => '/payments/processing'
            ];

        }

        return $urls;
    }

    /**
     * Get pages urls
     * @return array
     */
    public static function getPagesRules()
    {
        $urls = [];

        foreach (Files::find()->page()->active()->all() as $page) {
            $url = trim($page->url);
            $url = !empty($url) ? $url : '/';
            $url = str_replace('/', '\/', $url);
            $urls[$page->url] = [
                'rule' => "/^\/?{$url}$/i",
                'options' => [
                    'id' => $page->id,
                ],
                'url' => '/page/index'
            ];
        }

        return $urls;
    }
}