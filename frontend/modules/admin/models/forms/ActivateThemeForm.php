<?php

namespace frontend\modules\admin\models\forms;

use common\models\store\CustomThemes;
use common\models\stores\DefaultThemes;
use common\models\stores\Stores;
use frontend\modules\admin\models\search\ThemesSearch;
use Yii;
use yii\base\Exception;

/**
 * Class ActivateThemeForm
 * @package frontend\modules\admin\models\forms
 */
class ActivateThemeForm
{
    /**
     * Activate theme
     * @param $themeFolder
     * @return DefaultThemes | CustomThemes
     * @throws Exception
     */
    public static function activate($themeFolder)
    {

        $themeModel = (new ThemesSearch())->searchByFolder($themeFolder);
        if (!$themeModel) {
            throw new Exception('Theme does not exist in DB!');
        }

        $themePath = $themeModel->getThemePath();

        if (!file_exists($themePath)) {
            throw new Exception('Theme folder does not exist in filesystem!');
        }

        /** @var Stores $storeModel */
        $storeModel = Yii::$app->store->getInstance();

        $storeModel->setAttributes([
            'theme_name' => $themeModel->name,
            'theme_folder' => $themeModel->folder,
        ]);

        if (!$storeModel->save()) {
            throw new Exception('Could not update active theme settings!');
        }

        return $themeModel;
    }

}
