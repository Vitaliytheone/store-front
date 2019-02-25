<?php

namespace control_panel\controllers;

use control_panel\components\ActiveForm;
use control_panel\models\search\ActivitySearch;
use Yii;
use common\models\panels\Project;
use yii\web\Response;

/**
 * Class ActivityController
 * @package control_panel\controllers
 */
class ActivityController extends CustomController
{
    /**
     * View panel activity log list
     * @param $id
     * @return array|string|void
     * @throws \yii\base\ExitException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionIndex($id)
    {
        $panel = $this->_findModel($id);
        $this->activeTab = $panel->child_panel == 0 ? 'panels' : 'child-panels';

        $this->view->title = Yii::t('app', 'pages.title.activity', [
            'panel' => mb_strtolower($panel->getSite())
        ]);

        $logsSearch = new ActivitySearch();
        $logsSearch->setPanel($panel);
        $logsSearch->setParams(Yii::$app->request->get());

        if ($logsSearch->isChildHidePanel()) {
            $this->redirect('/');
            return Yii::$app->end();
        }

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
