<?php

namespace my\controllers;

use my\components\ActiveForm;
use my\models\search\ActivitySearch;
use Yii;
use common\models\panels\Project;
use yii\web\Response;

/**
 * Class ActivityController
 * @package my\controllers
 */
class ActivityController extends CustomController
{

    /**
     * View panel activity log list
     * @param integer $id
     * @return array|string
     * @throws \yii\base\ExitException
     */
    public function actionIndex($id)
    {
        $panel = $this->_findModel($id);

        $this->view->title = Yii::t('app', 'pages.title.activity', [
            'panel' => mb_strtolower($panel->getSite())
        ]);

        $logsSearch = new ActivitySearch();
        $logsSearch->setPanel($panel);
        $logsSearch->setParams(Yii::$app->request->get());

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $data = [];

            if (!$logsSearch->validate()) {
                return [
                    'error' => ActiveForm::firstError($logsSearch)
                ];
            }

            $actions = trim(Yii::$app->request->get('actions'));

            if (empty($actions)) {
                $actions = ['items', 'activity', 'events', 'accounts'];
            } else {
                $actions = (array)explode(",", $actions);
            }

            unset($_GET['action']);

            if (in_array('items', $actions)) {
                $searchItems = $logsSearch->search();
                $data['items'] = $this->renderPartial('layouts/_activity_items', [
                    'logItems' => $searchItems,
                ]);
                $data['pagination'] = $this->renderPartial('layouts/_activity_pagination', [
                    'logItems' => $searchItems,
                ]);
            }

            if (in_array('activity', $actions)) {
                $data['activity'] = $logsSearch->getActivity();
                $data['interval'] = $logsSearch->getInterval();
            }

            if (in_array('events', $actions)) {
                $data['events'] = $logsSearch->getEventsByGroups();
            }

            if (in_array('accounts', $actions)) {
                $data['accounts'] = $logsSearch->getAccounts();
            }

            return $data;
        }

        return $this->render('index', [
            'panel' => $panel,
            'filters' => $logsSearch->getParams(),
            'queryTypes' => $logsSearch->getQueryTypes()
        ]);
    }

    /**
     * Find model by id
     * @param int $id
     * @return Project
     * @throws \yii\base\ExitException
     */
    private function _findModel($id)
    {
        $model = Project::findOne([
            'cid' => Yii::$app->user->identity->id,
            'id' => $id,
        ]);

        if (!$model || !Project::hasAccess($model, 'canActivityLog')) {
            $this->redirect('/');
            return Yii::$app->end();
        }

        return $model;
    }
}
