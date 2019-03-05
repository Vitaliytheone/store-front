<?php
namespace sommerce\helpers;

use common\models\sommerce\Pages;
use common\models\sommerce\Products;
use Yii;
use common\models\sommerces\Stores;
use yii\helpers\ArrayHelper;

/**
 * Class RouteHelper
 * @package sommerce\helpers
 */
class RouteHelper {

    /**
     * Get routes
     * @param $sources
     * @return array
     */
    public static function getRoutes($sources = [
        'payments',
        'pages',
    ])
    {
        $urls = [];

        $urls = in_array('payments', $sources) ? ArrayHelper::merge($urls, static::getPaymentRules()) : [];
        $urls = in_array('pages', $sources) ? ArrayHelper::merge($urls, static::getPagesRules()) : [];

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
         * @var $store Stores
         */
        $store = Yii::$app->store->getInstance();

        $urls = [];
        foreach (CurrencyHelper::getPaymentsByCurrency($store->currency) as $method) {
            $methodUrls = is_array($method['url']) ? $method['url'] : [$method['url']];
            foreach ($methodUrls as $url) {
                if (empty($url)) {
                    continue;
                }
                $urls[$url] = [
                    'rule' => "/^\/?{$url}(?:\/(?<id>[\d]+))?.*?$/i",
                    'match' => [
                        'id'
                    ],
                    'options' => [
                        'method' => $method['code'],
                    ],
                    'url' => '/payments/result'
                ];
            }
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

        $pages = Pages::find()
            ->select(['url'])
            ->active()
            ->asArray()
            ->all();
        foreach ($pages as $page) {
            $url = trim($page['url']);
            $url = !empty($url) ? $url : '/';
            $url = str_replace('/', '\/', $url);
            $urls[$page['url']] = [
                'rule' => "/^\/?{$url}$/i",
                'options' => [
                    'url' => $page['url'],
                ],
                'url' => '/page/index'
            ];
        }

        return $urls;
    }
}