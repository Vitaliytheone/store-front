<?php

namespace frontend\modules\admin\controllers;

use common\components\ActiveForm;
use frontend\modules\admin\models\forms\CreateProviderForm;
use frontend\modules\admin\models\forms\ProvidersListForm;
use frontend\modules\admin\models\search\ProvidersSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;

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
        $this->view->title = 'Settings providers';
        
        $search = new ProvidersSearch();

        $this->addModule('adminProviders');

        $model = new ProvidersListForm();
        $model->setStore(Yii::$app->store->getInstance());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->refresh();
        }

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

    /**
     * Create provider
     *
     * @access public
     * @return mixed
     */
    public function actionCreateProvider()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new CreateProviderForm();
        $model->setStore(Yii::$app->store->getInstance());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                'status' => 'success',
            ];
        } else {
            return [
                'status' => 'error',
                'message' => ActiveForm::firstError($model)
            ];
        }
    }
}
