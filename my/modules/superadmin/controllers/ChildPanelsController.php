<?php

namespace my\modules\superadmin\controllers;

use my\components\ActiveForm;
use common\models\panels\Project;
use my\modules\superadmin\models\forms\UpgradePanelForm;
use my\modules\superadmin\models\search\PanelsSearch;
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
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET'],
                    'change-domain' => ['POST'],
                    'edit-expiry' => ['POST'],
                    'edit-providers' => ['POST'],
                    'edit' => ['POST'],
                    'generate-apikey' => ['GET'],
                    'upgrade' => ['POST'],
                    'change-status' => ['POST']
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
                    'edit'
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

        $filters = $panelsSearch->getParams();
        $status = ArrayHelper::getValue($filters, 'status');

        return $this->render('index', [
            'panels' => $panelsSearch->search(),
            'pageSizes' => PanelsSearch::getPageSizes(),
            'navs' => $panelsSearch->navs(),
            'status' => is_numeric($status) ? (int)$status : $status,
            'plans' => $panelsSearch->getAggregatedPlans(),
            'filters' => $filters
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
}
