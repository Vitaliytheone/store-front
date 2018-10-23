<?php

namespace my\modules\superadmin\controllers;

use common\models\panels\Domains;
use common\models\panels\ThirdPartyLog;
use my\modules\superadmin\models\search\DomainsSearch;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Account DomainsController for the `superadmin` module
 */
class DomainsController extends CustomController
{
    public $activeTab = 'domains';

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.domains');

        $domainsSearch = new DomainsSearch();
        $domainsSearch->setParams(Yii::$app->request->get());

        $status = Yii::$app->request->get('status', null);

        return $this->render('index', [
            'domains' => $domainsSearch->search(),
            'navs' => $domainsSearch->navs(),
            'status' => is_numeric($status) ? (int)$status : $status,
            'filters' => $domainsSearch->getParams()
        ]);
    }

    /**
     * Get domain details
     * @param int $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionDetails($id)
    {
        $domain = $this->findModel($id);

        Yii::$app->response->format = Response::FORMAT_JSON;

        $logs = ThirdPartyLog::find()->andWhere([
            'item_id' => $domain->id,
            'item' => [
                ThirdPartyLog::ITEM_BUY_DOMAIN,
                ThirdPartyLog::ITEM_PROLONGATION_DOMAIN,
            ],
        ])->all();

        return [
            'status' => 'success',
            'content' => $this->renderPartial('layouts/_domain_details', [
                'domain' => $domain,
                'logs' => $logs
            ])
        ];
    }

    /**
     * Find order model
     * @param $id
     * @return null|Domains
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = Domains::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        return $model;
    }
}
