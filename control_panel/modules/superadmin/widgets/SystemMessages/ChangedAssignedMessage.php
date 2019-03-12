<?php

namespace superadmin\widgets\SystemMessages;

/**
 * Widget for system message
 * Class ChangedAssignedMessage
 * @package superadmin\widgets\SystemMessages
 */
class ChangedAssignedMessage extends BaseSystemMessage
{
    /**
     * @return string
     */
    public function run()
    {
        return $this->render('_change_assigned_messsage', [
            'admin' => $this->admin,
            'date' => $this->date,
            'data' => $this->data,
            'admins' => $this->admins
        ]);
    }
}