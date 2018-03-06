<?php
namespace common\components\twig;

use common\components\twig\parsers\TokenParser_Include;
use common\models\store\Pages;
use frontend\helpers\AssetsHelper;
use Yii;
use Twig_SimpleFunction;

/**
 * Class Extension
 * @package common\components\twig
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
            new Twig_SimpleFunction('lang', function($value) {
                return Yii::t('app', $value, static::getTemplateVariables());
            }),
            new Twig_SimpleFunction('ceil', 'ceil'),
            new Twig_SimpleFunction('asset', function($value) {
                return AssetsHelper::getAssetPath() . $value;
            }),
            new Twig_SimpleFunction('page_url', function($pageId){
                $page = Pages::findOne($pageId);
                return '/' . ($page ? $page->url : '#');
            }),
        ];

        return $functions;
    }

    public function getTokenParsers()
    {
        return [
            new TokenParser_Include($this->twigOptions)
        ];
    }
}