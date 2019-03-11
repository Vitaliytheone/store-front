<?php
namespace superadmin\models\forms;

use common\models\sommerces\AdminUsers;
use common\models\sommerces\Customers;
use Yii;
use common\models\sommerces\TicketMessages;
use common\models\sommerces\Tickets;
use yii\base\Model;

/**
 * Class CreateTicketForm
 * @package superadmin\models\forms
 */
class CreateTicketForm extends Model
{
    public $subject;
    public $message;
    public $customer_id;

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
            [['message', 'subject', 'customer_id'], 'required'],
            [['subject'], 'string', 'max' => 300],
            [['message'], 'string', 'max' => 10000]
        ];
    }

    /**
     * @return bool
     * @throws \yii\db\Exception
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();

        $model = new Tickets();
        $model->subject = $this->subject;
        $model->customer_id = $this->customer_id;
        $model->is_admin = 1;
        $model->is_user = 0;
        $model->status = Tickets::STATUS_RESPONDED;
        
        if (!$model->save()) {
            $this->addErrors($model->getErrors());
            return false;
        }

        $ticketModel = new TicketMessages();
        $ticketModel->message = $this->message;
        $ticketModel->admin_id = $this->_user->id;
        $ticketModel->ticket_id = $model->id;
        $ticketModel->customer_id = 0;


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
            'customer_id' => Yii::t('app/superadmin', 'tickets.create.column_customer'),
            'subject' => Yii::t('app/superadmin', 'tickets.create.column_subject'),
            'message' => Yii::t('app/superadmin', 'tickets.create.column_message')
        ];
    }

    /**
     * Set ticket
     * @param AdminUsers $user
     */
    public function setUser($user)
    {
        $this->_user = $user;
    }
}