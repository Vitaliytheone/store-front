<?php
namespace my\models\forms;

use my\helpers\CurlHelper;
use my\helpers\UserHelper;
use common\models\panels\Customers;
use common\models\panels\Invoices;
use common\models\panels\MyActivityLog;
use common\models\panels\OrderLogs;
use common\models\panels\Orders;
use common\models\panels\Project;
use common\models\panels\TicketMessages;
use common\models\panels\Tickets;
use Yii;
use yii\base\Model;

/**
 * Class CreateMessageForm
 * @package common\models\panels\forms
 */
class CreateMessageForm extends Model
{
    public $message;

    /**
     * @var Tickets
     */
    public $_ticket;

    /**
     * @var Customers
     */
    public $_customer;

    /**
     * @var string
     */
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
     * Sign up method
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

        $this->_ticket->user = 1;
        $this->_ticket->date_update = time();
        $this->_ticket->status = Tickets::STATUS_PENDING;

        if (!$this->_ticket->save(false)) {
            $this->addError('message', Yii::t('app', 'error.ticket.can_not_create_message'));
            return false;
        }

        $ticketModel = new TicketMessages();
        $ticketModel->message = $this->message;
        $ticketModel->cid = $this->_customer->id;
        $ticketModel->tid = $this->_ticket->id;
        $ticketModel->date = time();
        $ticketModel->ip = $this->_ip ? $this->_ip : Yii::$app->request->userIP;

        if (!$ticketModel->save()) {
            $this->addError('message', Yii::t('app', 'error.ticket.can_not_create_message'));
            return false;
        }

        MyActivityLog::log(MyActivityLog::E_TICKETS_REPLY_TICKET, $ticketModel->id, $ticketModel->id, UserHelper::getHash());

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
