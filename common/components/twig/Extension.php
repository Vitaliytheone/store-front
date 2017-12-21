<?php
namespace common\components\twig;

use common\components\twig\parsers\TokenParser_Include;
use Yii;
use Twig_SimpleFunction;

/**
 * Class Extension
 * @package common\components\twig
 */
class Extension extends \Twig_Extension {

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
        ];

        return $functions;
    }

    public function getTokenParsers()
    {
        return [
            new TokenParser_Include()
        ];
    }
}