<?php

namespace sommerce\components\twig;

use common\models\store\PageFiles;
use sommerce\components\View;
use sommerce\helpers\PageFilesHelper;
use Twig_LoaderInterface;
use Twig_Environment;
use Twig_Loader_Array;
use Twig_Loader_Filesystem;
use yii\helpers\FileHelper;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class ViewRenderer
 * @package app\components\twig
 */
class ViewRenderer extends \common\components\twig\ViewRenderer
{

    /**
     * @var array
     */
    public static $pages;

    /**
     * @return Twig_LoaderInterface
     */
    public function getLoader()
    {
        if (null === $this->loader) {
            $files = [];

            foreach((array)ArrayHelper::getValue(PageFilesHelper::getFiles(), PageFiles::FILE_TYPE_TWIG, []) as $file) {
                $files['snippets/' . $file['file_name']] = $file['content'];
            }

            $this->loader = new Twig_Loader_Array($files);
        }

        return $this->loader;
    }




}