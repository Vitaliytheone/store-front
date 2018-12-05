<?php

namespace superadmin\controllers;

use my\components\ActiveForm;
use common\models\panels\Project;
use my\helpers\DomainsHelper;
use superadmin\models\forms\ChangeChildPanelProvider;
use superadmin\models\forms\UpgradePanelForm;
use superadmin\models\search\PanelsSearch;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use my\components\SuperAccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\AjaxFilter;
use \yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;

/**
 * Account ChildPanelsController for the `superadmin` module
 */
class ChildPanelsController extends PanelsController
{
    public $activeTab = 'child-panels';

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
            'ajax' => [
                'class' => AjaxFilter::class,
                'only' => [
                    'change-domain',
                    'edit-expiry',
                    'edit-providers',
                    'edit',
                    'generate-apikey',
                    'upgrade',
                    'edit-payment-methods',
                    'change-provider',
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['GET'],
                    'change-domain' => ['POST'],
                    'edit-expiry' => ['POST'],
                    'edit-providers' => ['POST'],
                    'edit' => ['POST'],
                    'generate-apikey' => ['GET'],
                    'upgrade' => ['POST'],
                    'change-status' => ['POST'],
                    'edit-payment-methods' => ['POST', 'GET'],
                ],
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => [
                    'change-domain',
                    'edit-expiry',
                    'edit-providers',
                    'generate-apikey',
                    'providers',
                    'upgrade',
                    'edit',
                    'edit-payment-methods',
                    'change-provider',
                    'get-providers',
                ],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    public function getViewPath()
    {
        return Yii::getAlias('@superadmin/views/panels');
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.child_panels');
        $params = Yii::$app->request->get();
        $params['child'] = 1;
        $panelsSearch = new PanelsSearch();
        $panelsSearch->setParams($params);
        $pageSize = Yii::$app->request->get('page_size');

        $filters = $panelsSearch->getParams();
        $status = ArrayHelper::getValue($filters, 'status');

        return $this->render('index', [
            'panels' => $panelsSearch->search(),
            'pageSizes' => PanelsSearch::getPageSizes(),
            'navs' => $panelsSearch->navs(),
            'status' => is_numeric($status) ? (int)$status : $status,
            'plans' => $panelsSearch->getAggregatedPlans(),
            'filters' => $filters,
            'pageSize' => $pageSize

        ]);
    }

    /**
     * Upgrade panel
     *
     * @access public
     * @param int $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionUpgrade($id)
    {
        $project = $this->findModel($id);
        $model = new UpgradePanelForm();
        $model->setProject($project);

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
     * @param int $id
     * @return array|void
     * @throws ForbiddenHttpException
     */
    public function actionDowngrade($id)
    {
        throw new ForbiddenHttpException();
    }

    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionGetProviders($id)
    {
        $panel = $this->findModel($id);
        $model = new ChangeChildPanelProvider();
        $model->setProject($panel);

        return [
            'status' => 'success',
            'content' => $model->getProviders()
        ];
    }

    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionChangeProvider($id)
    {
        $panel = $this->findModel($id);
        $model = new ChangeChildPanelProvider();
        $model->setProject($panel);

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
