<?php

namespace my\modules\superadmin\controllers;

use my\components\ActiveForm;
use my\helpers\Url;
use common\models\panels\TicketMessages;
use common\models\panels\Tickets;
use my\modules\superadmin\models\forms\CreateMessageForm;
use my\modules\superadmin\models\forms\CreateTicketForm;
use my\modules\superadmin\models\search\TicketsSearch;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Account TicketsController for the `superadmin` module
 */
class TicketsController extends CustomController
{
    public $activeTab = 'tickets';

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = Yii::t('app/superadmin', 'pages.title.tickets');

        $panelsSearch = new TicketsSearch();
        $panelsSearch->setParams(Yii::$app->request->get());

        $filters = $panelsSearch->getParams();
        $status = ArrayHelper::getValue($filters, 'status', 'all');

        return $this->render('index', [
            'tickets' => $panelsSearch->search(),
            'navs' => $panelsSearch->navs(),
            'status' => is_numeric($status) ? (int)$status : $status,
            'filters' => $panelsSearch->getParams()
        ]);
    }

    /**
     * Render view ticket with ticket messages
     * @param $id
     * @return string
     */
    public function actionView($id)
    {
        $ticket = $this->findModel($id);

        $this->view->title = Yii::t('app/superadmin', 'pages.title.ticket', [
            'id' => $id
        ]);

        $model = new CreateMessageForm();
        $model->setTicket($ticket);
        $model->setUser(Yii::$app->superadmin->getIdentity());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->refresh();
        }

        return $this->render('ticket', [
            'ticketMessages' => $model->getMessages(),
            'ticket' => $ticket,
            'model' => $model
        ]);
    }

    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new CreateTicketForm();
        $model->setUser(Yii::$app->superadmin->getIdentity());

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
     * Mark ticket unread
     * @param integer $id
     */
    public function actionMarkUnread($id)
    {
        $ticket = $this->findModel($id);

        $ticket->user = 1;
        $ticket->save(false);

        $this->redirect(Url::toRoute(['/tickets']));
    }

    /**
     * Change ticket status
     * @param integer $id
     * @param integer $status
     */
    public function actionChangeStatus($id, $status)
    {
        $ticket = $this->findModel($id);

        $ticket->status = $status;
        $ticket->save(false);

        $this->redirect(Url::toRoute(['/tickets']));
    }

    /**
     * Find ticket model
     * @param $id
     * @return null|Tickets
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = Tickets::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        return $model;
    }
}
