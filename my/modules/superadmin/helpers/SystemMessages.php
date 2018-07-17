<?php

namespace my\modules\superadmin\helpers;

use my\modules\superadmin\widgets\SystemMessages\BaseSystemMessage;
use my\modules\superadmin\widgets\SystemMessages\ChangedStatusMessage;
use my\modules\superadmin\widgets\SystemMessages\ChangedAssignedMessage;

class SystemMessages
{
    const TYPE_CHANGE_STATUS = 'change_status';
    const TYPE_CHANGED_ASSIGNED = 'change_assigned';

    /**
     * @return array
     */
    private static function _getConfigWidgets() {
        return [
            self::TYPE_CHANGE_STATUS => ChangedStatusMessage::class,
            self::TYPE_CHANGED_ASSIGNED => ChangedAssignedMessage::class,
        ];
    }

    /**
     * @param $data
     * @return BaseSystemMessage
     */
    public static function getSystemMessageWidget($data)
    {
        $config = static::_getConfigWidgets();
        if ($data && isset($config[$data->type])) {
            return $config[$data->type];
        }
        return null;
    }
}