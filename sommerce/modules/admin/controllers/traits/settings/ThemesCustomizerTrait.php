<?php

namespace sommerce\modules\admin\controllers\traits\settings;

use common\models\stores\Stores;
use Yii;
use yii\web\NotFoundHttpException;

trait ThemesCustomizerTrait
{
    /**
     * Customize theme page
     * @return string
     */
    public function actionCustomizeTheme($theme)
    {
        $this->view->title = Yii::t('admin', "settings.customize_theme_pagetitle", [
            'theme' => null
        ]);
        $this->layout = '@admin/views/layouts/react_app';

        /**
         * @var Stores $store
         */
        $store = Yii::$app->store->getInstance();


        $this->addModule('adminBlocks');

        return $this->render('customize_theme');
    }


    public function actionThemeGetStyle($theme)
    {
        $dir= Yii::getAlias('@defaultThemes/' . $theme);
        if (!is_dir($dir)) {
            $file = Yii::getAlias('@customThemes/' . $theme);
            if (!is_dir($file)) {
               throw new NotFoundHttpException();
            }
        }
        $style = require($dir .  '/style.css');
        if (!$style) {
            throw new NotFoundHttpException();
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        return $style;
    }
}