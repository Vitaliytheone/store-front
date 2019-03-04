<?php

namespace sommerce\components\twig;

use common\models\sommerce\Packages;
use sommerce\components\twig\parsers\TokenParser_Include;
use common\models\sommerce\Pages;
use sommerce\helpers\AssetsHelper;
use sommerce\helpers\PackageHelper;
use sommerce\helpers\PagesHelper;
use Yii;
use Twig_SimpleFunction;
use Twig_SimpleFilter;
use yii\helpers\ArrayHelper;

/**
 * Class Extension
 * @package sommerce\components\twig
 */
class Extension extends \Twig_Extension {

    protected $twigOptions;

    /**
     * Return predefined template variables
     * @return array
     */
    public static function getTemplateVariables()
    {
        return [
            'day' => date("d"),
            'month' => date("m"),
            'year' => date("Y"),
        ];
    }

    public function __construct($twigOptions = [])
    {
        $this->twigOptions = $twigOptions;
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        $functions = [
            new Twig_SimpleFunction('lang', function($value, $options = []) {
                return Yii::t('app', $value, array_merge(static::getTemplateVariables(), $options));
            }),
            new Twig_SimpleFunction('ceil', 'ceil'),
            new Twig_SimpleFunction('assets', function($value) {
                return AssetsHelper::getAssets($value);
            }),
            new Twig_SimpleFunction('page_url', function($pageId){
                $page = PagesHelper::getPageById($pageId);
                return ($page ? '/' . $page['url'] : '#');
            }),
            new Twig_SimpleFunction('package', function($packageId) {
                return PackageHelper::getPackageById($packageId);
            }),
            new Twig_SimpleFunction('products', function($productId) {
                return PackageHelper::getPackagesByProductId($productId);
            }),
        ];

        return $functions;
    }

    /**
     * @inheritdoc
     */
    public function getTokenParsers()
    {
        return [
            new TokenParser_Include($this->twigOptions)
        ];
    }

    /** @inheritdoc */
    public function getFilters()
    {
        $filters = [
            new Twig_SimpleFilter('money', function($price) {
                $currencyRule = ArrayHelper::getValue(Yii::$app->params['currencies'], Yii::$app->store->getInstance()->currency);
                if ($currencyRule && isset($currencyRule['money_format'])) {
                    $price = str_replace('{{number}}', $price, $currencyRule['money_format']);
                }
                return $price;
            })
        ];

        return $filters;
    }
}