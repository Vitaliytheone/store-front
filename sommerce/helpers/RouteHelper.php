<?php
namespace sommerce\helpers;

use common\helpers\CurrencyHelper;
use common\models\sommerce\Pages;
use common\models\sommerce\Products;
use common\models\sommerces\Stores;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class RouteHelper
 * @package sommerce\helpers
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
        $urls = ArrayHelper::merge($urls, static::getProductsRules());
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

        foreach (Pages::find()->active()->all() as $page) {
            $url = trim($page->url);
            $url = !empty($url) ? $url : '/';
            $url = str_replace('/', '\/', $url);
            $urls[$page->url] = [
                'rule' => "/^\/?{$url}$/i",
                'options' => [
                    'url' => $page->url,
                ],
                'url' => '/page/index'
            ];
        }

        return $urls;
    }

    /**
     * Get products urls
     * @return array
     */
    public static function getProductsRules()
    {
        $urls = [];

        $urls[] = [
            'rule' => "/^\/?product(?:\/(?<id>[\d]+))$/i",
            'match' => [
                'id'
            ],
            'url' => '/product/index'
        ];

        foreach (Products::find()->active()->all() as $product) {
            $url = trim($product->url, '/');
            $url = !empty($url) ? $url : '/';
            $url = str_replace('/', '\/', $url);
            $urls[$product->url] = [
                'rule' => "/^\/?{$url}$/i",
                'options' => [
                    'id' => $product->id,
                ],
                'url' => '/product/index'
            ];
        }

        return $urls;
    }
}