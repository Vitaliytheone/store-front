<?php

namespace my\modules\superadmin\controllers;

use my\components\ActiveForm;
use my\helpers\Url;
use common\models\panels\AdditionalServices;
use my\modules\superadmin\models\forms\EditProviderForm;
use my\modules\superadmin\models\search\ProvidersSearch;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use my\components\SuperAccessControl;
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
        return [
            'access' => [
                'class' => SuperAccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET'],
                    'edit' => ['POST'],
                ],
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => ['edit', 'get-panels', 'create'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
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
            'plans' => $providersSearch->getPlans(),
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

        $data = Yii::$app->request->post('EditProviderForm');
        $model = new EditProviderForm($data);
        $model->setProvider($provider);

        if ($model->save()) {
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

    public function actionCreate()
    {
        $model = new AdditionalServices();
        $data = Yii::$app->request->post('EditProviderForm');
        $model = $this->loadData($model, $data);
        $model->beforeSave(true);

        if ($model->save()) {
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
     * Change provider status action
     * @param integer $id
     * @param integer $status
     * @throws NotFoundHttpException
     */
    public function actionChangeStatus($id, $status)
    {
        $provider = $this->findModel($id);

        $provider->changeStatus($status);

        $this->redirect(Url::toRoute(['/providers']));
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

    /**
     * @param AdditionalServices $provider
     * @param array $data
     * @return AdditionalServices
     */
    protected function loadData(AdditionalServices $provider, array $data)
    {
        foreach ($data as $key => $value) {
            $provider->$key = $value;
        }

        return $provider;
    }
}
