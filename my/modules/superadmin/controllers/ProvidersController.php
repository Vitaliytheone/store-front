<?php

namespace superadmin\controllers;

use my\components\ActiveForm;
use my\helpers\Url;
use common\models\panels\AdditionalServices;
use superadmin\models\forms\CreateProviderForm;
use superadmin\models\forms\EditProviderForm;
use superadmin\models\search\ProvidersSearch;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;

/**
 * ProvidersController for the `superadmin` module
 */
class ProvidersController extends CustomController
{
    public $activeTab = 'providers';

    public $layout = 'superadmin_v2.php';

    public function behaviors()
    {
        return array_merge(parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'index' => ['GET'],
                        'edit' => ['POST'],
                        'create' => ['POST'],
                    ],
                ],
                'ajax' => [
                    'class' => AjaxFilter::class,
                    'only' => ['create', 'edit']
                ],
                'content' => [
                    'class' => ContentNegotiator::class,
                    'only' => ['edit', 'get-panels', 'create'],
                    'formats' => [
                        'application/json' => Response::FORMAT_JSON,
                    ],
                ],
            ]);
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.providers');

        ini_set('memory_limit', '256M');

        $providersSearch = new ProvidersSearch();
        $providersSearch->setParams(Yii::$app->request->get());

        $type = Yii::$app->request->get('type', null);

        return $this->render('index', [
            'providers' => $providersSearch->search(),
            'navs' => $providersSearch->navs(),
            'type' => is_numeric($type) ? (int)$type : $type,
            'filters' => $providersSearch->getParams(),
            'scripts' => $providersSearch->getScripts(),
        ]);
    }

    /**
     * Edit provider settings
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionEdit($id)
    {
        $provider = $this->findModel($id);

        $model = new EditProviderForm();
        $model->setProvider($provider);

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

    /**
     * Create new provider
     * @return array
     */
    public function actionCreate()
    {
        $model = new CreateProviderForm();

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

    /**
     * Search log action
     * Moved to logs/providers
     * @return string
     */
    public function actionSearchLog()
    {
        return $this->redirect(Url::toRoute('logs/providers'));
    }

    /**
     * Get provider panels
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionGetPanels($id)
    {
        $provider = $this->findModel($id);
        $use = Yii::$app->request->get('use');

        if ($use) {
            $projects = $provider->getUseProjects();
        } else {
            $projects = $provider->getProjects();
        }

        return [
            'content' => $this->renderPartial('layouts/_projects_modal_content', [
                'projects' => $projects
            ])
        ];
    }

    /**
     * Find provider model
     * @param $id
     * @return null|AdditionalServices
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = AdditionalServices::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        return $model;
    }
}
