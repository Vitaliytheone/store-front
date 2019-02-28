<?php
namespace store\components\twig;

use store\components\twig\parsers\TokenParser_Include;
use common\models\store\Pages;
use store\helpers\AssetsHelper;
use store\helpers\PackageHelper;
use Yii;
use Twig_SimpleFunction;
use Twig_SimpleFilter;
use yii\helpers\ArrayHelper;

/**
 * Class Extension
 * @package store\components\twig
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
            new Twig_SimpleFunction('asset', function($value) {
                return AssetsHelper::getAssetPath() . $value;
            }),
            new Twig_SimpleFunction('page_url', function($pageId){
                $page = Pages::find()->active()->andWhere([
                    'id' => $pageId,
                ])->one();
                return '/' . ($page ? $page->url : '#');
            }),
            new Twig_SimpleFunction('package', function($value) {
                return PackageHelper::getPackageBuyNow($value);
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