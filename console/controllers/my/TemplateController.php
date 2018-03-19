<?php
namespace console\controllers\my;

use Yii;
use yii\helpers\FileHelper;

/**
 * Generate js templates
 * Class TemplateController
 * @package console\controllers\my
 */
class TemplateController extends CustomController
{
    public function actionIndex()
    {
        $webRoot = Yii::getAlias('@my') . '/web';
        $basePath = $webRoot . '/themes/templates';
        $jstUrl = '/themes/js/app/templates.js';

        $files = FileHelper::findFiles($basePath, array('fileTypes' => array('html')));
        $js = '';
        foreach ($files as $file) {
            $templateName = str_replace($basePath . DIRECTORY_SEPARATOR, '', $file);
            $templateName = str_replace('\\', '/', $templateName);
            $templateName = str_replace('.html', '', $templateName);
            $html = trim(file_get_contents($file));
            $html = strtr($html, array("\t"=>'\t',"\n"=>'\n',"\r"=>'\r','"'=>'\"','\''=>'\\\'','\\'=>'\\\\','</'=>'<\/'));
            $js .= "\n\ntemplates['{$templateName}'] = _.template(\""
                . $html . "\");";
        }
        $js = <<<JS
                var templates = {};
                {$js}
JS;

        $path = $webRoot . $jstUrl;

        @unlink($path);
        file_put_contents($path, $js);
        chmod($path, 0666);
    }
}