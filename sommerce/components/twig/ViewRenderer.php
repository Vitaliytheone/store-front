<?php

namespace sommerce\components\twig;

use common\models\store\PageFiles;
use sommerce\helpers\PageFilesHelper;
use Twig_LoaderInterface;
use Twig_Loader_Array;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class ViewRenderer
 * @package sommerce\components\twig
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

            foreach ((array)ArrayHelper::getValue(PageFilesHelper::getFilesGroupByType(), PageFiles::FILE_TYPE_TWIG, []) as $file) {
                $files['/snippets/' . $file['file_name']] = $file['content'];
            }

            $this->loader = new Twig_Loader_Array($files);
        }

        return $this->loader;
    }

}