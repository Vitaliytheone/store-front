<?php

namespace my\modules\superadmin\widgets\SystemMessages;

/**
 * Widget for system message
 * Class ChangedAssignedMessage
 * @package my\modules\superadmin\widgets\SystemMessages
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