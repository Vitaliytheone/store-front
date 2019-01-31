<?php
namespace common\components\twig;

use common\components\twig\parsers\TokenParser_Include;
use Yii;
use Twig_SimpleFunction;
use Twig_SimpleFilter;

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
            new Twig_SimpleFunction('lang', function($value, $options = []) {
                return Yii::t('app', $value, array_merge(static::getTemplateVariables(), $options));
            }),
            new Twig_SimpleFunction('ceil', 'ceil'),
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
                return $price;
            })
        ];

        return $filters;
    }
}