<?php

namespace my\models\forms;

use common\components\cdn\BaseCdn;
use common\models\panels\TicketFiles;
use my\helpers\UserHelper;
use common\models\panels\Customers;
use common\models\panels\MyActivityLog;
use common\models\panels\TicketMessages;
use common\models\panels\Tickets;
use Yii;
use yii\base\Model;

/**
 * Class CreateMessageForm
 * @package my\models\forms
 *
 * @property Tickets $ticket
 * @property string $ip
 * @property BaseCdn $cdn
 * @property Customers $customer
 */
class CreateMessageForm extends Model
{
    public $message;

    public $post;

    /** @var Tickets */
    public $_ticket;

    /** @var Customers */
    public $_customer;

    /** @var string */
    public $_ip;

    /** @var BaseCdn|\common\components\cdn\providers\Uploadcare */
    public $_cdn;

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
     * Set cdn
     * @param BaseCdn $cdn
     */
    public function setCdn(BaseCdn $cdn)
    {
        $this->_cdn = $cdn;
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

        $this->_ticket->is_user = 1;
        $this->_ticket->updated_at = time();
        $this->_ticket->status = Tickets::STATUS_PENDING;

        if (!$this->_ticket->save(false)) {
            $this->addError('message', Yii::t('app', 'error.ticket.can_not_create_message'));
            return false;
        }

        $ticketModel = new TicketMessages();
        $ticketModel->message = $this->message;
        $ticketModel->customer_id = $this->_customer->id;
        $ticketModel->ticket_id = $this->_ticket->id;
        $ticketModel->created_at = time();
        $ticketModel->ip = $this->_ip ? $this->_ip : Yii::$app->request->userIP;

        if (!$ticketModel->save()) {
            $this->addError('message', Yii::t('app', 'error.ticket.can_not_create_message'));
            return false;
        }


        $link = $this->post;
        if (!empty($link)) {
            $ticketFilesModel = new TicketFiles();
            $ticketFilesModel->customer_id = $this->_customer->id;
            $ticketFilesModel->ticket_id = $this->_ticket->id;
            $ticketFilesModel->message_id = $ticketModel->id;
            $ticketFilesModel->link = $link;
            $ticketFilesModel->cdn_id = $this->_cdn->getId($link);
            $ticketFilesModel->created_at = time();
            $ticketFilesModel->setDetails($this->_cdn->getFiles($link, true));

            if (!$ticketFilesModel->save()) {
                $this->addError('message', Yii::t('app', 'error.ticket.can_not_attach_files'));
                return false;
            }
            $this->_cdn->storeGroup($link);
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
