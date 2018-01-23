<?php
namespace common\components\twig;

use common\components\twig\parsers\TokenParser_Include;
use frontend\helpers\AssetsHelper;
use Yii;
use Twig_SimpleFunction;

/**
 * Class Extension
 * @package common\components\twig
 */
class Extension extends \Twig_Extension {

    protected $twigOptions;

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
                return Yii::t('app', $value);
            }),
            new Twig_SimpleFunction('ceil', 'ceil'),
            new Twig_SimpleFunction('asset', function($value) {
                return AssetsHelper::getAssetPath() . $value;
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