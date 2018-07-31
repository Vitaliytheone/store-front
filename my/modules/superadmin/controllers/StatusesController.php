<?php

namespace my\modules\superadmin\controllers;

use my\helpers\Url;
use Yii;
use my\components\SuperAccessControl;
use my\modules\superadmin\models\forms\DatetimepickerForm;
use my\modules\superadmin\models\search\GetstatusSearch;

/**
 * StatusesController for the `superadmin` module
 */
class StatusesController extends CustomController
{
    public $activeTab = 'statuses';
    public $layout = 'superadmin_v2.php';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => SuperAccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->redirect(Url::toRoute('statuses/getstatus', true));
    }

    /**
     * Renders the getstatus view
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionGetstatus()
    {
        $this->view->title = 'Getstatus';

        $customersSearch = new GetstatusSearch();
        $customersSearch->setParams(Yii::$app->request->get());

        return $this->render('getstatus', [
            'statuses' => $customersSearch->getStatuses(),
            'datetime' => $customersSearch->getDatetime(),
        ]);
    }
}
