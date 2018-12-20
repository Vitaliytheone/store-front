<?php

namespace superadmin\controllers;


use common\models\gateways\Sites;
use my\components\SuperAccessControl;
use my\helpers\Url;
use superadmin\models\search\GatewaysSearch;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use Yii;

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
                ],
            ],
//            'ajax' => [
//                'class' => AjaxFilter::class,
//                'only' => ['ajax-customers', 'edit', 'set-password']
//            ],
//            'content' => [
//                'class' => ContentNegotiator::class,
//                'only' => ['edit', 'set-password', 'ajax-customers'],
//                'formats' => [
//                    'application/json' => Response::FORMAT_JSON,
//                ],
//            ],
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
        $site = Sites::findOne($id);
        $site->changeStatus($status);
        $this->redirect(Url::toRoute('/'. $this->activeTab));
    }

    /**
     * Change panel domain.
     *
     * @access public
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionChangeDomain($id)
    {
        $site = Sites::findOne($id);
        $domain = Yii::$app->request->post();
var_dump($domain);die;
        if ($site->changeDomain()) {
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
