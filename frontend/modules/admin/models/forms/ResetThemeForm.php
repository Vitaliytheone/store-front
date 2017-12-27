<?php

namespace frontend\modules\admin\models\forms;

use common\models\store\CustomThemes;
use common\models\stores\DefaultThemes;
use yii\base\Model;

/**
 * Class EditThemeForm
 * @package frontend\modules\admin\models\forms
 */
class ResetThemeForm extends Model
{
    /**
     * Reset default theme file
     * @param $themeFolderName
     * @param $resetFileName
     * @return bool
     */
    public static function reset($themeFolderName, $resetFileName)
    {
        $file = trim(escapeshellarg($resetFileName),'\'');

        $themeModel = DefaultThemes::findOne(['folder' => $themeFolderName]);

        if (!$themeModel && !$themeModel->isActive()) {
            return false;
        }

        $pathToFile = CustomThemes::getThemesPath() . '/' . $themeModel->folder . '/' . $file;

        if (!file_exists($pathToFile)) {
            return false;
        }

        return unlink($pathToFile);
    }
}
