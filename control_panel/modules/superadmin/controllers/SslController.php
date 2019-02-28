<?php

namespace superadmin\controllers;

use common\models\sommerces\SslCert;
use common\models\sommerces\ThirdPartyLog;
use superadmin\models\forms\DisableSslForm;
use superadmin\models\search\SslSearch;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Account SslController for the `superadmin` module
 */
class SslController extends CustomController
{
    public $activeTab = 'ssl';

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.ssl');

        $sslSearch = new SslSearch();
        $sslSearch->setParams(Yii::$app->request->get());

        $status = Yii::$app->request->get('status', null);

        return $this->render('index', [
            'sslList' => $sslSearch->search(),
            'navs' => $sslSearch->navs(),
            'status' => is_numeric($status) ? (int)$status : $status,
            'filters' => $sslSearch->getParams()
        ]);
    }

    /**
     * Get ssl details
     * @param int $id
     * @return array
     */
    public function actionDetails($id)
    {
        $ssl = $this->findModel($id);

        Yii::$app->response->format = Response::FORMAT_JSON;

        $logs = ThirdPartyLog::find()->andWhere([
            'item_id' => $ssl->id,
            'item' => [
                ThirdPartyLog::ITEM_BUY_SSL,
                ThirdPartyLog::ITEM_PROLONGATION_SSL,
            ]
        ])->all();

        return [
            'status' => 'success',
            'content' => $this->renderPartial('layouts/_ssl_details', [
                'ssl' => $ssl,
                'logs' => $logs
            ])
        ];
    }

    /**
     * Disable SSL action
     * @param $id
     * @return array
     */
    public function actionDisable($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $form = new DisableSslForm();
        $form->setSsl($this->findModel($id));

        if (!$form->disabled() || $form->hasErrors()) {
            return ['status' => 'error'];
        }

        return ['status' => 'success'];
    }

    /**
     * Find order model
     * @param $id
     * @return null|SslCert
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = SslCert::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        return $model;
    }
}
