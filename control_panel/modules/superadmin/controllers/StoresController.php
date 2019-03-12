<?php

namespace superadmin\controllers;

use common\models\sommerces\SuperAdmin;
use common\models\sommerces\SuperAdminToken;
use control_panel\components\SuperAccessControl;
use common\models\sommerces\Stores;
use control_panel\components\ActiveForm;
use control_panel\helpers\Url;
use superadmin\models\forms\ChangeStoreDomainForm;
use superadmin\models\forms\EditStoreExpiryForm;
use superadmin\models\search\StoresSearch;
use superadmin\models\forms\EditStoreForm;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\AjaxFilter;
use yii\filters\VerbFilter;
use yii\filters\ContentNegotiator;

/**
 * Controller StoresController for the `superadmin` module
 */
class StoresController extends CustomController
{
    public $activeTab = 'stores';

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
                'only' => ['edit-store', 'edit-expiry', 'change-domain']
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['GET'],
                    'edit-store' => ['POST'],
                    'edit-expiry' => ['POST'],
                    'change-status' => ['POST'],
                    'change-domain' => ['POST'],
                ],
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => ['edit-store', 'edit-expiry', 'change-domain'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * Renders the index view for the module
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.stores');

        $params = Yii::$app->request->get();
        $params['child'] = 1;
        $storesSearch = new StoresSearch();
        $storesSearch->setParams($params);

        $filters = $storesSearch->getParams();
        $status = ArrayHelper::getValue($filters, 'status');

        return $this->render('index', [
            'stores' => $storesSearch->search(),
            'navs' => $storesSearch->navs(),
            'status' => is_numeric($status) ? (int)$status : $status,
            'filters' => $storesSearch->getParams()
        ]);
    }

    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionEditStore($id)
    {
        $store = $this->_findStore($id);

        $model = new EditStoreForm();
        $model->setStore($store);

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
     * Change store expired.
     *
     * @access public
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionEditExpiry($id)
    {
        $store = $this->_findStore($id);

        $model = new EditStoreExpiryForm();
        $model->setStore($store);

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
     * Change project status
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionChangeStatus()
    {
        $id = Yii::$app->request->post('id');
        $status = Yii::$app->request->post('status');

        $store = $this->_findStore($id);

        $store->changeStatus($status);

        $this->redirect(Url::toRoute('/stores'));
    }

    /**
     * Change store domain.
     *
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\base\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionChangeDomain($id)
    {
        $store = $this->_findStore($id);

        $model = new ChangeStoreDomainForm();
        $model->setStore($store);

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
     * Sign in as admin store
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionSignInAsAdmin($id)
    {
        $store = $this->_findStore($id);

        $sommerceDomain = $store->getSommerceDomain();

        if (!$sommerceDomain) {
            throw new NotFoundHttpException();
        }

        /**
         * @var SuperAdmin $superUser
         */
        $superUser = Yii::$app->superadmin->getIdentity();
        $token = SuperAdminToken::getToken($superUser->id, SuperAdminToken::ITEM_SOMMERCE, $store->id);

        return $this->redirect('http://' . $sommerceDomain->domain . '/admin/super-login?token=' . $token);
    }

    /**
     * Find store
     * @param int $id
     * @return Stores
     * @throws NotFoundHttpException
     */
    protected function _findStore(int $id)
    {
        $store = Stores::findOne((int)$id);

        if (!$store) {
            throw new NotFoundHttpException();
        }

        return $store;
    }
}
