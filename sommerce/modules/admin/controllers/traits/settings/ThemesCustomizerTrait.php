<?php

namespace sommerce\modules\admin\controllers\traits\settings;

use common\models\stores\Stores;
use Yii;
use yii\web\BadRequestHttpException;
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


        $this->addModule('adminCustomizeTheme');

        return $this->render('customize_theme');
    }


    public function actionThemeGetResource($theme, $type)
    {
        $resource = '';
        if ($type == 'style') {
            $resource = 'style.css';
        } else if ($type == 'data') {
            $resource = 'config.json';
        }
        if (!$resource) {
            throw new BadRequestHttpException();
        }
        $dir= Yii::getAlias('@defaultThemes/' . $theme);
        if (!is_dir($dir)) {
            $file = Yii::getAlias('@customThemes/' . $theme);
            if (!is_dir($file)) {
               throw new NotFoundHttpException();
            }
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $content = file_get_contents($dir .  '/' . $resource);
        if (!$content) {
            throw new NotFoundHttpException();
        }
        return $content;
    }
}