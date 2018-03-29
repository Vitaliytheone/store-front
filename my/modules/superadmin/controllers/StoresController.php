<?php

namespace my\modules\superadmin\controllers;

use common\models\stores\Stores;
use my\components\ActiveForm;
use my\helpers\Url;
use my\modules\superadmin\models\forms\ChangeStoreDomainForm;
use my\modules\superadmin\models\forms\EditStoreExpiryForm;
use my\modules\superadmin\models\search\StoresSearch;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Controller StoresController for the `superadmin` module
 */
class StoresController extends CustomController
{
    public $activeTab = 'stores';

    /**
     * Renders the index view for the module
     * @return string
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
     * Change store expired.
     *
     * @access public
     * @param int $id
     * @return mixed
     */
    public function actionEditExpiry($id)
    {
        $store = $this->_findStore($id);

        Yii::$app->response->format = Response::FORMAT_JSON;

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
     * @param $id
     * @param $status
     * @throws NotFoundHttpException
     */
    public function actionChangeStatus($id, $status)
    {
        $store = $this->_findStore($id);

        $store->changeStatus($status);

        $this->redirect(Url::toRoute('/stores'));
    }

    /**
     * Change store domain.
     *
     * @access public
     * @param int $id
     * @return mixed
     */
    public function actionChangeDomain($id)
    {
        $store = $this->_findStore($id);

        Yii::$app->response->format = Response::FORMAT_JSON;

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
     * Find store
     * @param int $id
     * @return Stores
     * @throws NotFoundHttpException
     */
    protected function _findStore(int $id)
    {
        $store =  Stores::findOne((int)$id);

        if (!$store) {
            throw new NotFoundHttpException();
        }

        return $store;
    }
}
