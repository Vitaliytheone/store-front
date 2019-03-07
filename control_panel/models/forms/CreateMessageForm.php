<?php

namespace control_panel\models\forms;

use common\models\sommerces\Customers;
use common\models\sommerces\MyActivityLog;
use common\models\sommerces\TicketMessages;
use common\models\sommerces\Tickets;
use control_panel\helpers\UserHelper;
use Yii;
use yii\base\Model;

/**
 * Class CreateMessageForm
 * @package control_panel\models\forms
 *
 * @property Tickets $ticket
 * @property string $ip
 * @property Customers $customer
 */
class CreateMessageForm extends Model
{
    public $message;

    /** @var string */
    public $post;

    /** @var Tickets */
    public $_ticket;

    /** @var Customers */
    public $_customer;

    /** @var string */
    public $_ip;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['message'], 'filter', 'filter' => function($value) { // Trim input values
                return is_string($value) || is_numeric($value) ? trim((string)$value) : null;
            }],
            [['message'], 'required'],
            [['message'], 'string', 'max' => 1000]
        ];
    }

    /**
     * Set ticket
     * @param Tickets $ticket
     */
    public function setTicket(Tickets $ticket)
    {
        $this->_ticket = $ticket;
    }

    /**
     * Set customer
     * @param Customers $customer
     */
    public function setCustomer(Customers $customer)
    {
        $this->_customer = $customer;
    }

    /**
     * Set ip
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->_ip = $ip;
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

        if ($this->_ticket->status == Tickets::STATUS_CLOSED) {
            $this->addError('message', Yii::t('app', 'error.ticket.can_not_create_message'));
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();

        $this->_ticket->is_user = 1;
        $this->_ticket->updated_at = time();
        $this->_ticket->status = Tickets::STATUS_PENDING;

        if (!$this->_ticket->save(false)) {
            $this->addError('message', Yii::t('app', 'error.ticket.can_not_create_message'));
            $transaction->rollBack();
            return false;
        }

        $ticketModel = new TicketMessages();
        $ticketModel->message = $this->message;
        $ticketModel->customer_id = $this->_customer->id;
        $ticketModel->ticket_id = $this->_ticket->id;
        $ticketModel->admin_id = 0;
        $ticketModel->created_at = time();
        $ticketModel->ip = $this->_ip ? $this->_ip : Yii::$app->request->userIP;
        $ticketModel->post = $this->post;

        if (!$ticketModel->save()) {
            $this->addError('message', Yii::t('app', 'error.ticket.can_not_create_message'));
            $transaction->rollBack();
            return false;
        }

        MyActivityLog::log(MyActivityLog::E_TICKETS_REPLY_TICKET, $ticketModel->id, $ticketModel->id, UserHelper::getHash());

        $transaction->commit();

        return true;
    }

    /**
     * Notice
     */
    public function notice()
    {
        Yii::$app->mailer->compose(
            ['html' => 'new_message_email'],
            ['ticket' => $this->_ticket, 'message' => $this->message]
        )
            ->setFrom(Yii::$app->params['noreplyEmail'])
            ->setTo(Yii::$app->params['sysmailSupportEmail'])
            ->setSubject('New reply #' . $this->_ticket->id)
            ->send();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'message' => Yii::t('app', 'form.create_message.message'),
        ];
    }
}
