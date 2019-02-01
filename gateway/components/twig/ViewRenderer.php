<?php
namespace gateway\components\twig;

use Twig_LoaderInterface;
use common\models\gateway\Files;
use gateway\helpers\FilesHelper;
use Yii;
use Twig_Loader_Array;
use yii\helpers\ArrayHelper;

/**
 * Class ViewRenderer
 * @package gateway\components\twig
 */
class ViewRenderer extends \common\components\twig\ViewRenderer
{
    /**
     * @return Twig_Loader_Array|Twig_LoaderInterface
     */
    public function getLoader()
    {
        if (null == $this->loader) {
            $files = [];

            foreach((array)ArrayHelper::getValue(FilesHelper::getFiles(), Files::FILE_TYPE_SNIPPET, []) as $file) {
                $files['/snippets/' . $file['name']] = $file['content'];
            }

            $this->loader = new Twig_Loader_Array($files);
        }

        return $this->loader;
    }
}