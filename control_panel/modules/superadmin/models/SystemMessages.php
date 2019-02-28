<?php

namespace superadmin\models;

use common\models\sommerces\TicketMessages;
use yii\base\Model;

class SystemMessages extends Model
{

    /**
     * @param array $options
     * @param int $ticketId
     * @param int $adminId
     * @return bool
     */
    public static function add($options, $ticketId, $adminId)
    {
        $model = new TicketMessages();
        if ($options['from'] == $options['to']) {
            return false;
        }
        $model->setSystemInfo($options);
        $model->admin_id = $adminId;
        $model->customer_id = 0;
        $model->ticket_id = $ticketId;
        $model->save(false);
        return true;
    }
}