<?php

namespace my\modules\superadmin\controllers;

use my\components\ActiveForm;
use my\helpers\StringHelper;
use my\helpers\Url;
use common\models\panels\Project;
use my\modules\superadmin\models\forms\ChangeDomainForm;
use my\modules\superadmin\models\forms\EditExpiryForm;
use my\modules\superadmin\models\forms\EditProjectForm;
use my\modules\superadmin\models\forms\EditProvidersForm;
use my\modules\superadmin\models\forms\UpgradePanelForm;
use my\modules\superadmin\models\search\PanelsSearch;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Account ChildPanelsController for the `superadmin` module
 */
class ChildPanelsController extends CustomController
{
    public $activeTab = 'child-panels';

    public function getViewPath()
    {
        return Yii::getAlias('@superadmin/views/child_panels');
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

        $this->redirect(Url::toRoute('/child-panels'));
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

        $key = StringHelper::randomString(32, 'abcdefghijklmnopqrstuwxyz0123456789');

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
     * Upgrade panel
     *
     * @access public
     * @param int $id
     * @return array
     */
    public function actionUpgrade($id)
    {
        if (!($project = Project::findOne($id))) {
            throw new NotFoundHttpException();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

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
}
