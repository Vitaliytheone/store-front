<?php

namespace superadmin\models\forms;

use common\components\cdn\BaseCdn;
use common\models\panels\AdminUsers;
use common\models\panels\SuperAdmin;
use common\models\panels\TicketFiles;
use Yii;
use common\models\panels\TicketMessages;
use common\models\panels\Tickets;
use yii\base\Model;

/**
 * Class CreateMessageForm
 * @package superadmin\models\forms
 *
 * @property Tickets $ticket
 * @property SuperAdmin $user
 * @property array|\yii\db\ActiveRecord[] $messages
 * @property BaseCdn $cdn
 */
class CreateMessageForm extends Model
{
    /** @var string */
    public $message;

    public $post;

    /** @var Tickets $_ticket; */
    protected $_ticket;

    /** @var AdminUsers $_user; */
    protected $_user;

    /** @var BaseCdn|\common\components\cdn\providers\Uploadcare */
    public $_cdn;

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
     * Set cdn
     * @param BaseCdn $cdn
     */
    public function setCdn(BaseCdn $cdn)
    {
        $this->_cdn = $cdn;
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }


        $this->_ticket->status = Tickets::STATUS_RESPONDED;
        $this->_ticket->is_admin = 1;

        if (!$this->_ticket->save(false)) {
            $this->addError('message', Yii::t('app', 'error.ticket.can_not_create_message'));
            return false;
        }

        $ticketModel = new TicketMessages();
        $ticketModel->message = $this->message;
        $ticketModel->admin_id = $this->_user->id;
        $ticketModel->ticket_id = $this->_ticket->id;
        $ticketModel->customer_id = 0;

        if (!$ticketModel->save()) {
            $this->addError('message', Yii::t('app', 'error.ticket.can_not_create_message'));
            return false;
        }


        $link = $this->post;
        if (!empty($link)) {
            $ticketFilesModel = new TicketFiles();
            $ticketFilesModel->customer_id = 0;
            $ticketFilesModel->ticket_id = $this->_ticket->id;
            $ticketFilesModel->admin_id = $this->_user->id;
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