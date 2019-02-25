<?php

namespace superadmin\controllers;

use control_panel\helpers\Url;
use superadmin\models\search\SenderSearch;
use superadmin\models\search\SubscriptionSearch;
use Yii;
use control_panel\components\SuperAccessControl;
use superadmin\models\forms\DatetimepickerForm;
use superadmin\models\search\GetstatusSearch;

/**
 * StatusesController for the `superadmin` module
 */
class StatusesController extends CustomController
{
    public $activeTab = 'statuses';

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
        $this->view->title = Yii::t('app/superadmin', 'getstatus.title');

        $customersSearch = new GetstatusSearch();
        $customersSearch->setParams(Yii::$app->request->get());

        return $this->render('getstatus', [
            'statuses' => $customersSearch->getStatuses(),
            'datetime' => $customersSearch->getDatetime(),
        ]);
    }

    public function actionSubscription()
    {
        $this->view->title = Yii::t('app/superadmin', 'subscription.title');

        $model = new SubscriptionSearch();

        return $this->render('subscription', [
            'model' => $model->search(),
        ]);
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSender()
    {
        $this->view->title = Yii::t('app/superadmin', 'sender.title');

        $model = new SenderSearch();
        $model->setParams(Yii::$app->request->get());

        return $this->render('sender', [
            'model' => $model->search(),
            'datetime' => $model->getDatetime(true)
        ]);
    }
}
