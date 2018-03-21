<?php
namespace my\modules\superadmin\models\forms;

use common\models\panels\AdminUsers;
use common\models\panels\Customers;
use Yii;
use common\models\panels\TicketMessages;
use common\models\panels\Tickets;
use yii\base\Model;

/**
 * Class CreateTicketForm
 * @package my\modules\superadmin\models\forms
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

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();

        $model = new Tickets();
        $model->subject = $this->subject;
        $model->cid = $this->customer_id;
        $model->admin = 1;
        $model->status = Tickets::STATUS_RESPONDED;
        
        if (!$model->save()) {
            $this->addErrors($model->getErrors());
            return false;
        }

        $ticketModel = new TicketMessages();
        $ticketModel->message = $this->message;
        $ticketModel->uid = $this->_user->id;
        $ticketModel->tid = $model->id;
        $ticketModel->date = time();
        $ticketModel->ip = Yii::$app->request->userIP;

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

    /**
     * Get active customers
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getCustomers()
    {
        return Customers::find()->andWhere([
            'status' => Customers::STATUS_ACTIVE
        ])->all();
    }
}