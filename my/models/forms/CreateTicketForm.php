<?php

namespace my\models\forms;

use my\helpers\UserHelper;
use common\models\panels\Customers;
use common\models\panels\MyActivityLog;
use common\models\panels\TicketMessages;
use common\models\panels\Tickets;
use Yii;
use yii\base\Model;

/**
 * Class CreateTicketForm
 * @package my\models\forms
 *
 * @property string $ip
 * @property Customers $customer
 */
class CreateTicketForm extends Model
{
    public $subject;
    public $message;

    /** @var string */
    public $post;

    /**
     * @var Customers
     */
    public $_customer;

    /**
     * @var string $_ip
     */
    public $_ip;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['subject'], 'ticketsLimit'],

            [['message', 'subject'], 'filter', 'filter' => function($value) { // Trim input values
                return is_string($value) || is_numeric($value) ? trim((string)$value) : null;
            }],

            [['message', 'subject'], 'required'],
            [['message'], 'string', 'max' => 1000],
            [['subject'], 'string', 'max' => 300],
        ];
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
     * Added pending tickets validation
     * @param $attribute
     * @return bool
     */
    public function ticketsLimit($attribute)
    {
        if ($this->hasErrors()) {
            return false;
        }

        if (!Tickets::canCreate($this->_customer->id)) {
            $this->addError($attribute, Yii::t('app', 'error.ticket.tickets_limit_exceeded'));
            return false;
        }

        return true;
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
     * Create ticket method
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();

        $model = new Tickets();
        $model->subject = $this->subject;
        $model->customer_id = $this->_customer->id;
        $model->is_user = 1;
        if (!$model->save()) {
            $this->addErrors($model->getErrors());
            return false;
        }

        $ticketModel = new TicketMessages();
        $ticketModel->message = $this->message;
        $ticketModel->customer_id = $this->_customer->id;
        $ticketModel->ticket_id = $model->id;
        $ticketModel->admin_id = 0;
        $ticketModel->created_at = time();
        $ticketModel->ip = $this->_ip ? $this->_ip : Yii::$app->request->userIP;
        $ticketModel->post = $this->post;

        if (!$ticketModel->save()) {
            $this->addError('message', Yii::t('app', 'error.ticket.can_not_create_message'));
            $transaction->rollBack();
            return false;
        }

        $transaction->commit();

        MyActivityLog::log(MyActivityLog::E_TICKETS_CREATE_TICKET, $model->id, $model->id, UserHelper::getHash());

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'subject' => Yii::t('app', 'form.create_ticket.subject'),
            'message' => Yii::t('app', 'form.create_ticket.message')
        ];
    }

    /**
     * Notice
     * @param Tickets $model
     */
    public function notice(Tickets $model)
    {
        Yii::$app->mailer->compose(
            ['html' => 'new_ticket_email'],
            ['ticket' => $model, 'message' => $this->message]
        )
            ->setFrom(Yii::$app->params['noreplyEmail'])
            ->setTo(Yii::$app->params['sysmailSupportEmail'])
            ->setSubject('New ticket #' . $model->id)
            ->send();
    }
}
