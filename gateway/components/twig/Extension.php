<?php
namespace gateway\components\twig;

use common\components\twig\parsers\TokenParser_Include;
use common\models\gateway\Files;
use gateway\helpers\AssetsHelper;
use gateway\helpers\FilesHelper;
use Yii;
use Twig_SimpleFunction;

/**
 * Class Extension
 * @package gateway\components\twig
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
                return AssetsHelper::getAssetPath() . "/" . trim(((string)$value), "/");
            }),
            new Twig_SimpleFunction('page_url', function($pageId){
                $page = FilesHelper::getFileById(Files::FILE_TYPE_PAGE, $pageId);
                return '/' . ($page ? $page['url'] : '#');
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
        $filters = [];

        return $filters;
    }
}