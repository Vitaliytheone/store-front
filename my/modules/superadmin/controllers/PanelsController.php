<?php

namespace my\modules\superadmin\controllers;

use common\models\panels\PanelDomains;
use common\models\panels\SuperAdmin;
use common\models\panels\SuperAdminToken;
use my\components\ActiveForm;
use my\helpers\StringHelper;
use my\helpers\Url;
use common\models\panels\Project;
use my\modules\superadmin\models\forms\ChangeDomainForm;
use my\modules\superadmin\models\forms\DowngradePanelForm;
use my\modules\superadmin\models\forms\EditExpiryForm;
use my\modules\superadmin\models\forms\EditProjectForm;
use my\modules\superadmin\models\forms\EditProvidersForm;
use my\modules\superadmin\models\search\PanelsSearch;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Account PanelsController for the `superadmin` module
 */
class PanelsController extends CustomController
{
    public $activeTab = 'panels';

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.panels');

        $panelsSearch = new PanelsSearch();
        $panelsSearch->setParams(Yii::$app->request->get());

        $filters = $panelsSearch->getParams();
        $status = ArrayHelper::getValue($filters, 'status');

        return $this->render('index', [
            'panels' => $panelsSearch->search(),
            'navs' => $panelsSearch->navs(),
            'status' => is_numeric($status) ? (int)$status : $status,
            'plans' => $panelsSearch->getAggregatedPlans(),
            'filters' => $panelsSearch->getParams()
        ]);
    }

    /**
     * Change project status
     * @param $id
     * @param $status
     * @throws NotFoundHttpException
     */
    public function actionChangeStatus($id, $status)
    {
        $project =  Project::findOne($id);

        if (!$project) {
            throw new NotFoundHttpException();
        }


        $project->changeStatus($status);

        $this->redirect(Url::toRoute('/panels'));
    }

    /**
     * Change panel domain.
     *
     * @access public
     * @param int $id
     * @return mixed
     */
    public function actionChangeDomain($id)
    {
        if (!($project = Project::findOne($id))) {
            throw new NotFoundHttpException();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new ChangeDomainForm();
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
     * Change panel expired.
     *
     * @access public
     * @param int $id
     * @return mixed
     */
    public function actionEditExpiry($id)
    {
        if (!($project = Project::findOne($id))) {
            throw new NotFoundHttpException();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new EditExpiryForm();
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
     * Change panel providers.
     *
     * @access public
     * @param int $id
     * @return mixed
     */
    public function actionEditProviders($id)
    {
        if (!($project = Project::findOne($id))) {
            throw new NotFoundHttpException();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new EditProvidersForm();
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
     * Edit panel.
     *
     * @access public
     * @param int $id
     * @return mixed
     */
    public function actionEdit($id)
    {
        if (!($project = Project::findOne($id))) {
            throw new NotFoundHttpException();
        }

        $this->view->title = Yii::t('app/superadmin', 'pages.title.edit_panel', [
            'domain' => $project->getSite()
        ]);

        $model = new EditProjectForm();
        $model->setProject($project);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->redirect(Url::toRoute('/panels'));
        }

        return $this->render('edit', [
            'model' => $model
        ]);
    }

    /**
     * Generate uniq project apikey
     * @return array
     */
    public function actionGenerateApikey()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $key = StringHelper::randomString(64, 'abcdefghijklmnopqrstuwxyz0123456789');

        do {
            if (!Project::find()->andWhere([
                'apikey' => $key
            ])->exists()) {
                return [
                    'key' => $key
                ];
            }
        } while(true);
    }

    /**
     * Get providers
     * @param integer $id
     */
    public function actionProviders($id)
    {
        if (!($project = Project::findOne($id))) {
            throw new NotFoundHttpException();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new DowngradePanelForm();
        $model->setProject($project);

        return [
            'providers' => $model->getProviders()
        ];
    }

    /**
     * Downgrade panel
     *
     * @access public
     * @param int $id
     * @return array
     */
    public function actionDowngrade($id)
    {
        if (!($project = Project::findOne($id))) {
            throw new NotFoundHttpException();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new DowngradePanelForm();
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
     * Sign in as admin panel
     *
     * @access public
     * @param int $id
     * @return Response
     */
    public function actionSignInAsAdmin($id)
    {
        if (!($project = Project::findOne($id))) {
            throw new NotFoundHttpException();
        }

        if (!($panelDomain = PanelDomains::find()->andWhere([
            'panel_id' => $project->id,
            'type' => PanelDomains::TYPE_SUBDOMAIN
        ])->andFilterWhere([
            'AND',
            ['like', 'domain', '.' . Yii::$app->params['panelDomain']],
        ])->one())) {
            throw new NotFoundHttpException();
        }

        /**
         * @var SuperAdmin $superUser
         */
        $superUser = Yii::$app->superadmin->getIdentity();
        $token = SuperAdminToken::getToken($superUser->id, SuperAdminToken::ITEM_PANELS, $project->id);

        return $this->redirect('http://' . $panelDomain->domain . '/admin/default/check?id=' . $token);
    }

}
