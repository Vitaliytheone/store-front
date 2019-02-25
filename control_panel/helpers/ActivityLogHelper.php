<?php
namespace control_panel\helpers;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class ActivityLogHelper
 * @package control_panel\helpers
 */
class ActivityLogHelper {

    /**
     * Get event name by event code
     * @param integer $eventCode
     * @return string|null
     */
    public static function getEventName($eventCode)
    {
        return ArrayHelper::getValue(static::getEvents(), $eventCode);
    }

    /**
     * Get events
     * @return array
     */
    public static function getEvents()
    {
        return (array)Yii::$app->params['activityTypes'];
    }
}