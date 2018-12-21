<?php

namespace superadmin\controllers;


use common\models\gateways\Sites;
use my\components\ActiveForm;
use my\components\SuperAccessControl;
use my\helpers\Url;
use superadmin\models\forms\ChangeGatewayDomainForm;
use superadmin\models\forms\EditGatewayExpiryForm;
use superadmin\models\search\GatewaysSearch;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class GatewaysController
 * @package superadmin\controllers
 */
class GatewaysController extends CustomController
{
    public $activeTab = 'gateways';

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
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
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['GET'],
                    'change-status' => ['POST'],
                    'change-domain' => ['POST'],
                    'edit-expiry' => ['POST'],
                ],
            ],
            'ajax' => [
                'class' => AjaxFilter::class,
                'only' => ['change-domain', 'edit-expiry']
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => ['change-domain', 'edit-expiry'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ]);
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.gateways');

        $params = Yii::$app->request->get();
        $params['child'] = 1;
        $panelsSearch = new GatewaysSearch();
        $panelsSearch->setParams($params);
        $pageSize = Yii::$app->request->get('page_size');

        $filters = $panelsSearch->getParams();
        $status = ArrayHelper::getValue($filters, 'status');

        return $this->render('index', [
            'gateways' => $panelsSearch->search(),
            'navs' => $panelsSearch->navs(),
            'status' => is_numeric($status) ? (int)$status : $status,
            'filters' => $filters,
            'pageSize' => $pageSize

        ]);
    }

    /**
     * @throws \yii\base\Exception
     */
    public function actionChangeStatus()
    {
        $id = Yii::$app->request->post('id');
        $status = Yii::$app->request->post('status');
        $site = $this->findModel($id);
        $site->changeStatus($status);
        $this->redirect(Url::toRoute('/'. $this->activeTab));
    }

    /**
     * Change panel domain.
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionChangeDomain($id)
    {
        $model = new ChangeGatewayDomainForm();
        $gateway = $this->findModel($id);
        $model->setGateway($gateway);

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
     * Change panel expired.
     *
     * @access public
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionEditExpiry($id)
    {
        $gateway = $this->findModel($id);
        $model = new EditGatewayExpiryForm();
        $model->setGateway($gateway);

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
     * @param $attributes
     * @return Sites|null
     * @throws NotFoundHttpException
     */
    private function findModel($attributes)
    {
        $site = Sites::findOne($attributes);

        if (!$site) {
            throw new NotFoundHttpException();
        }

        return $site;
    }
}
