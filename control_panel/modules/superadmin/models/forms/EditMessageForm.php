<?php
namespace superadmin\models\forms;

use common\models\sommerces\AdminUsers;
use common\models\sommerces\SuperAdmin;
use Yii;
use common\models\sommerces\TicketMessages;
use common\models\sommerces\Tickets;
use yii\base\Model;

/**
 * Class CreateMessageForm
 * @package superadmin\models\forms
 */
class EditMessageForm extends Model
{
    /**
     * @var string
     */
    public $message;

    /**
     * @var Tickets
     */
    protected $_ticket;

    /**
     * @var TicketMessages
     */
    protected $_ticketMessage;

    /**
     * @var AdminUsers
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

    public function setMessage(TicketMessages $message)
    {
        $this->_ticketMessage = $message;
    }

    /**
     * @param Tickets $ticket
     */
    public function setTicket(Tickets $ticket)
    {
        $this->_ticket = $ticket;
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();


        $this->_ticketMessage->message = $this->message;

        if (!$this->_ticketMessage->save()) {
            $this->addError('message', Yii::t('app', 'error.ticket.can_not_edit_message'));
            return false;
        }

        $this->_ticket->updated_at = time();
        if (!$this->_ticket->save(false)) {
            $this->addError('message', Yii::t('app', 'error.ticket.can_not_edit_message'));
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

    /**
     * Set ticket
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
}