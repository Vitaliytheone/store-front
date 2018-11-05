<?php
namespace superadmin\models\search;

use common\models\panels\SuperAdmin;
use common\models\panels\TicketMessages;
use Yii;

/**
 * Search messages for ticket
 * Class TicketMessages
 * @package superadmin\models\search
 */
class TicketMessagesSearch
{
    /**
     * @var int
     */
    private $_ticketId;

    /**
     * @var null|array
     */
    private $_messages = null;

    /**
     * @var null|int
     */
    private $_indexCustomerMessages = null;

    /**
     * @var SuperAdmin
     */
    private $_admin;

    /**
     * TicketMessagesSearch constructor
     * @param int $ticketId
     */
    public function __construct($ticketId)
    {
        $this->_ticketId = $ticketId;
    }

    /**
     * @param SuperAdmin $admin
     */
    public function setUser($admin)
    {
        $this->_admin = $admin;
    }

    /**
     * Get ticket messages
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getMessages()
    {
        if ($this->_messages == null) {
            $this->_messages = TicketMessages::find()->where([
                'ticket_id' => $this->_ticketId
            ])
                ->joinWith(['customer', 'admin', 'customer.actualProjects'])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();
        }

        return $this->_messages;
    }

    /**
     * @return int
     */
    private function getIndexCustomerMessage() {
        if ($this->_indexCustomerMessages === null) {
            $index = -1;
            for ($i = 0; $i < count($this->_messages); $i++) {
                if ($this->_messages[$i]->admin_id != $this->_admin->id) {
                    $index = $i;
                    break;
                }
            }
            $this->_indexCustomerMessages = $index;
        }

        return $this->_indexCustomerMessages;
    }

    /**
     * @param int $index
     * @return bool
     */
    public function canEdit($index)
    {
        if ($index >= count($this->_messages)) {
            return false;
        }
        $message = $this->_messages[$index];
        $indexCustomerMessage = $this->getIndexCustomerMessage();
        if (($indexCustomerMessage < 0 || $index < $indexCustomerMessage)
            && $this->_admin->id == $message->admin_id) {
            return true;
        }

        return false;
    }

}