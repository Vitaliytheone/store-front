<?php

namespace sommerce\components\twig;

use common\models\sommerce\PageFiles;
use sommerce\helpers\PageFilesHelper;
use Twig_Loader_Array;
use Twig_LoaderInterface;
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
                $content = $file['content'] ?? '';
                $files['/snippets/' . $file['file_name']] = $content;
            }

            $this->loader = new Twig_Loader_Array($files);
        }

        return $this->loader;
    }

}