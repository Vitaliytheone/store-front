<?php

namespace frontend\modules\admin\controllers;

use frontend\modules\admin\models\search\ProvidersSearch;
use Yii;
use yii\filters\AccessControl;

/**
 * Settings controller for the `admin` module
 */
class SettingsController extends CustomController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
        ];
    }

    /**
     * Settings general
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Settings providers
     * @return string
     */
    public function actionProviders()
    {
        $search = new ProvidersSearch();

        return $this->render('providers', [
            'providers' => $search->search()
        ]);
    }

    /**
     * Settings payments
     * @return string
     */
    public function actionPayments()
    {
        return $this->render('payments');
    }

    /**
     * Settings themes
     * @return string
     */
    public function actionThemes()
    {
        return $this->render('themes');
    }

    /**
     * Settings pages
     * @return string
     */
    public function actionPages()
    {
        return $this->render('pages');
    }

    /**
     * Settings blocks
     * @return string
     */
    public function actionBlocks()
    {
        return $this->render('blocks');
    }

    /**
     * Settings navigations
     * @return string
     */
    public function actionNavigations()
    {
        return $this->render('navigations');
    }
}
