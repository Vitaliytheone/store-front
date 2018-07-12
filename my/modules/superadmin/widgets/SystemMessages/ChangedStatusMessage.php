<?php

namespace my\modules\superadmin\widgets\SystemMessages;

class ChangedStatusMessage extends BaseSystemMessage
{
    public function run()
    {
        return $this->render('_change_status_message', [
            'admin' => $this->admin,
            'date' => $this->date,
            'data' => $this->data,
            'admins' => $this->admins
        ]);
    }
}