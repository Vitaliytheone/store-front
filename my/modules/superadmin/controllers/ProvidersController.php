<?php

namespace my\modules\superadmin\controllers;

use my\components\ActiveForm;
use my\helpers\Url;
use common\models\panels\AdditionalServices;
use my\modules\superadmin\models\forms\EditProviderForm;
use my\modules\superadmin\models\search\ProvidersSearch;
use my\modules\superadmin\models\search\ProviderLogsSearch;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * ProvidersController for the `superadmin` module
 */
class ProvidersController extends CustomController
{
    public $activeTab = 'providers';

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
            'filters' => $providersSearch->getParams()
        ]);
    }

    /**
     * Edit provider action
     * @param integer $id
     * @return array
     */
    public function actionEdit($id)
    {
        $provider = $this->findModel($id);

        Yii::$app->response->format = Response::FORMAT_JSON;

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
     * Change provider status action
     * @param integer $id
     * @param integer $status
     * @return array
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
     */
    public function actionGetPanels($id)
    {
        $provider = $this->findModel($id);
        $use = Yii::$app->request->get('use');

        Yii::$app->response->format = Response::FORMAT_JSON;

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
