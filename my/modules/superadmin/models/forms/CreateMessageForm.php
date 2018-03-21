<?php
namespace my\modules\superadmin\models\forms;

use common\models\panels\AdminUsers;
use Yii;
use common\models\panels\TicketMessages;
use common\models\panels\Tickets;
use yii\base\Model;

/**
 * Class CreateMessageForm
 * @package my\modules\superadmin\models\forms
 */
class CreateMessageForm extends Model
{
    public $message;

    /**
     * @var Tickets $_ticket;
     */
    protected $_ticket;

    /**
     * @var AdminUsers $_user;
     */
    protected $_user;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['message'], 'required'],
            [['message'], 'string', 'max' => 10000]
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }


        $this->_ticket->status = Tickets::STATUS_RESPONDED;
        $this->_ticket->admin = 1;
        $this->_ticket->date_update = time();

        if (!$this->_ticket->save(false)) {
            $this->addError('message', Yii::t('app', 'error.ticket.can_not_create_message'));
            return false;
        }

        $ticketModel = new TicketMessages();
        $ticketModel->message = $this->message;
        $ticketModel->uid = $this->_user->id;
        $ticketModel->tid = $this->_ticket->id;
        $ticketModel->date = time();
        $ticketModel->ip = Yii::$app->request->userIP;

        if (!$ticketModel->save()) {
            $this->addError('message', Yii::t('app', 'error.ticket.can_not_create_message'));
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'message' => Yii::t('app/superadmin', 'tickets.create.column_message')
        ];
    }

    /**
     * Set ticket
     * @param Tickets $ticket
     */
    public function setTicket(Tickets $ticket)
    {
        $this->_ticket = $ticket;

        $this->_ticket->user = 0;
        $this->_ticket->save(false);
    }

    /**
     * Set ticket
     * @param AdminUsers $user
     */
    public function setUser($user)
    {
        $this->_user = $user;
    }

    /**
     * Get ticket messages
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getMessages()
    {
        return TicketMessages::find()->where([
                'tid' => $this->_ticket->id
            ])
            ->joinWith(['customer', 'admin', 'customer.actualProjects'])
            ->orderBy(['date' => SORT_ASC])
            ->all();
    }
}