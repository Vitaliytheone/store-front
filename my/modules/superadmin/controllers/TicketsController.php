<?php

namespace my\modules\superadmin\controllers;

use common\models\panels\SuperAdmin;
use common\models\panels\TicketMessages;
use my\components\ActiveForm;
use my\helpers\Url;
use my\components\SuperAccessControl;
use common\models\panels\Tickets;
use my\modules\superadmin\helpers\SystemMessages;
use my\modules\superadmin\models\forms\CreateMessageForm;
use my\modules\superadmin\models\forms\CreateTicketForm;
use my\modules\superadmin\models\search\TicketBlocksSearch;
use my\modules\superadmin\models\search\TicketMessagesSearch;
use my\modules\superadmin\models\search\TicketsSearch;
use my\modules\superadmin\models\SystemMessages as ModelSystemMessages;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use yii\filters\AjaxFilter;
use \yii\filters\VerbFilter;

/**
 * Account TicketsController for the `superadmin` module
 */
class TicketsController extends CustomController
{
    public $activeTab = 'tickets';
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
                    ]
                ],
            ],
            'ajax' => [
                'class' => AjaxFilter::class,
                'only' => ['create', 'edit-message']
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET'],
                    'view'=> ['GET', 'POST'],
                    'create' => ['POST'],
                    'mark-unread' => ['GET'],
                    'change-status' => ['POST'],
                    'change-assigned' => ['POST'],
                    'delete-message' => ['POST'],
                    'edit-message' => ['POST']
                ],
            ],
            'content' => [
                'class' => ContentNegotiator::class,
                'only' => ['create', 'edit-message'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
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
        $this->view->title = Yii::t('app/superadmin', 'pages.title.tickets');

        $panelsSearch = new TicketsSearch();

        $panelsSearch->setParams(Yii::$app->request->get());

        $filters = $panelsSearch->getParams();
        $status = ArrayHelper::getValue($filters, 'status', 'all');
        $assignee = ArrayHelper::getValue($filters, 'assignee', 'all');

        return $this->render('index', [
            'tickets' => $panelsSearch->search(),
            'navs' => $panelsSearch->navs(),
            'status' => is_numeric($status) ? (int)$status : $status,
            'assignee' => is_numeric($assignee) ? (int)$assignee : $assignee,
            'filters' => $panelsSearch->getParams(),
            'superAdmins' => $panelsSearch->getCountsByAssignee(),
            'superAdminCount' => $panelsSearch->getSuperadminsCount()
        ]);
    }

    /**
     * Render view ticket with ticket messages
     * @param $id
     * @return string
     */
    public function actionView($id)
    {
        /**
         * @var Tickets $ticket
         * @var SuperAdmin $admin
         */
        $ticket = $this->findModel($id);
        $admin = Yii::$app->superadmin->getIdentity();

        $this->view->title = Yii::t('app/superadmin', 'pages.title.ticket', [
            'id' => $id
        ]);

        $model = new CreateMessageForm();
        $model->setTicket($ticket);
        $model->setUser($admin);

        $blocks = TicketBlocksSearch::search($ticket->customer_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $this->refresh();
        }

        $ticketMessagesSearch = new TicketMessagesSearch($ticket->id);
        $ticketMessagesSearch->setUser($admin);
        $ticketMessages = $ticketMessagesSearch->getMessages();

        return $this->render('ticket', [
            'ticketMessages' => $ticketMessages,
            'ticketMessagesSearch' => $ticketMessagesSearch,
            'admins' => SuperAdmin::find()->indexBy('id')->all(),
            'ticket' => $ticket,
            'statuses' => Tickets::getStatuses(),
            'model' => $model,
            'stores' => $blocks['stores'],
            'domains' => $blocks['domains'],
            'ssl' => $blocks['ssl'],
            'panels' => $blocks['panels'],
            'childPanels' => $blocks['childPanels']
        ]);
    }

    /**
     * Create ticket
     * @return array
     */
    public function actionCreate()
    {
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

        $ticket->is_user = 1;
        $ticket->save(false);

        $this->redirect(Url::toRoute(['/tickets']));
    }

    /**
     * Change ticket status
     */
    public function actionChangeStatus()
    {
        $params = Yii::$app->request->post();
        if (!empty($params['id']) && isset($params['status'])) {
            $ticket = $this->findModel($params['id']);
            $oldStatus = $ticket->status;
            $ticket->status = $params['status'];
            if ($ticket->save()) {
                ModelSystemMessages::add([
                        'type' => SystemMessages::TYPE_CHANGE_STATUS,
                        'from' => $oldStatus,
                        'to' => $params['status']
                    ],
                    $ticket->id,
                    Yii::$app->superadmin->getIdentity()->id
                );
            }
            return $this->redirect(Url::toRoute(['/tickets/view', 'id' => $params['id']]));
        }

        throw new ForbiddenHttpException();
    }

    /**
     * Change ticket status
     */
    public function actionChangeAssigned()
    {
        $params = Yii::$app->request->post();
        if (!empty($params['assigned-select']) && !empty($params['ticketId'])) {
            $ticket = $this->findModel($params['ticketId']);
            $options = [
                'type' => SystemMessages::TYPE_CHANGED_ASSIGNED,
                'from' => $ticket->assigned_admin_id,
                'to' => $params['assigned-select'],
                'comment' => empty($params['comment']) ? '' :$params['comment']
            ];

            if (ModelSystemMessages::add(
                $options,
                $ticket->id,
                Yii::$app->superadmin->getIdentity()->id
            )) {
                $ticket->assigned_admin_id = $params['assigned-select'];
                $ticket->save(false);
            }
            return $this->redirect(Url::toRoute(['/tickets/view', 'id' => $params['ticketId']]));
        }

        throw new ForbiddenHttpException();
    }

    /**
     * Delete ticket message
     */
    public function actionDeleteMessage()
    {
        $params = Yii::$app->request->post();
        if (!empty($params['ticketId']) && !empty($params['messageId'])) {
            $id = $params['messageId'];
            $ticketId = $params['ticketId'];
            $message = $this->findMessage($id);
            if ($message->canAdminEdit()) {
                $message->delete();
            }
            return $this->redirect(Url::toRoute(['/tickets/view', 'id' => $ticketId]));
        }

        throw new ForbiddenHttpException();
    }


    /**
     * Edit ticket message
     */
    public function actionEditMessage()
    {
        $params = Yii::$app->request->post();
        if (!empty($params['ticketId']) && isset($params['message'])) {
            $ticketId = $params['ticketId'];
            $id = $params['messageId'];
            $message = $this->findMessage($id);
            $message->message = $params['message'];

            if ($message->canAdminEdit()) {
                $model = new CreateMessageForm();
                $model->message = $message->message;
                if ($model->validate()) {
                    $message->save();
                }  else {
                    return [
                        'status' => 'error',
                        'message' => ActiveForm::firstError($model)
                    ];
                }
            }

            return $this->redirect(Url::toRoute(['/tickets/view', 'id' => $ticketId]));
        }

        throw new ForbiddenHttpException();
    }

    /**
     * Find ticket model
     * @param int $id
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


    /**
     * Find ticket message model
     * @param $id
     * @return null|TicketMessages
     * @throws NotFoundHttpException
     */
    protected function findMessage($id)
    {
        $model = TicketMessages::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException();
        }

        return $model;
    }
}
