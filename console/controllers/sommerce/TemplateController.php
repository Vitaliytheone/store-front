<?php
namespace console\controllers\sommerce;

use Yii;
use yii\base\Exception;
use yii\helpers\FileHelper;

/**
 * Generate js templates
 * Class TemplateController
 * @package console\controllers\sommerce
 */
class TemplateController extends CustomController
{
    const PATH_FRONTEND = 'frontend';
    const PATH_GLOBAL = 'global';
    const PATH_ADMIN = 'admin';

    /**
     * Generate underscore JS templates for admin and frontend path
     */
    public function actionIndex()
    {
        $webRoot = Yii::getAlias('@sommerce') . '/web';

        foreach ([self::PATH_FRONTEND, self::PATH_ADMIN, self::PATH_GLOBAL] as $path) {
            $sourcePath = $webRoot . '/js/templates/' . $path;
            $destinationFile = $webRoot . '/js/app/' . $path . '/templates.js';

            $this->_generateTemplates($path, $sourcePath, $destinationFile);
        }
    }

    /**
     * Generate underscore JS templates
     * @param $sourcePath
     * @param $destinationFile
     */
    protected function _generateTemplates($pathName, $sourcePath, $destinationFile)
    {
        $files = FileHelper::findFiles($sourcePath, array('fileTypes' => array('html')));
        $js = '';
        foreach ($files as $file) {
            $templateName = str_replace($sourcePath . DIRECTORY_SEPARATOR, '', $file);
            $templateName = str_replace('\\', '/', $templateName);
            $templateName = str_replace('.html', '', $templateName);

            if ($pathName === self::PATH_GLOBAL) {
                $templateName = $pathName . '/' . $templateName;
            }

            $html = trim(file_get_contents($file));
            $html = strtr($html, array("\t"=>'\t',"\n"=>'\n',"\r"=>'\r','"'=>'\"','\''=>'\\\'','\\'=>'\\\\','</'=>'<\/'));
            $js .= "\n\ntemplates['{$templateName}'] = _.template(\""
                . $html . "\");";
        }
        $js = <<<JS
                var templates = {};
                {$js}
JS;

        $path = $destinationFile;

        @unlink($path);
        file_put_contents($path, $js);
        chmod($path, 0666);
    }
}