<?php

namespace superadmin\models\forms;

use common\models\sommerces\AdminUsers;
use common\models\sommerces\SuperAdmin;
use common\models\sommerces\TicketMessages;
use common\models\sommerces\Tickets;
use Yii;
use yii\base\Model;

/**
 * Class CreateMessageForm
 * @package superadmin\models\forms
 *
 * @property Tickets $ticket
 * @property SuperAdmin $user
 * @property array|\yii\db\ActiveRecord[] $messages
 */
class CreateMessageForm extends Model
{
    /** @var string */
    public $message;

    /** @var string */
    public $post;

    /** @var Tickets $_ticket; */
    protected $_ticket;

    /** @var AdminUsers $_user; */
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

    /**
     * Set ticket
     * @param Tickets $ticket
     */
    public function setTicket(Tickets $ticket)
    {
        $this->_ticket = $ticket;
        $this->_ticket->is_user = 0;
        $this->_ticket->save(false);
    }

    /**
     * Set user
     * @param SuperAdmin $user
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
            'ticket_id' => $this->_ticket->id
        ])
            ->joinWith(['customer', 'admin', 'customer.actualProjects'])
            ->orderBy(['created_at' => SORT_ASC])
            ->all();
    }

    /**
     * Save ticket message
     * @return bool
     * @throws \yii\db\Exception
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();

        $this->_ticket->status = Tickets::STATUS_RESPONDED;
        $this->_ticket->is_admin = 1;

        if (!$this->_ticket->save(false)) {
            $this->addError('message', Yii::t('app', 'error.ticket.can_not_create_message'));
            $transaction->rollBack();
            return false;
        }

        $ticketModel = new TicketMessages();
        $ticketModel->message = $this->message;
        $ticketModel->admin_id = $this->_user->id;
        $ticketModel->ticket_id = $this->_ticket->id;
        $ticketModel->customer_id = 0;
        $ticketModel->post = $this->post;

        if (!$ticketModel->save()) {
            $this->addError('message', Yii::t('app', 'error.ticket.can_not_create_message'));
            $transaction->rollBack();
            return false;
        }

        $transaction->commit();

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
}