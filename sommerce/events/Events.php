<?php
namespace sommerce\events;

use yii\base\Event;

/**
 * Class Events
 * @package sommerce\events
 */
class Events
{
    const EVENT_SOMMERCE_ORDER_CHANGED_STATUS = 'sommerce_order_changed_status';

    const EVENT_SOMMERCE_ORDER_CONFIRM = 'sommerce_order_confirm';
    const EVENT_SOMMERCE_ORDER_IN_PROGRESS = 'sommerce_order_in_progress';
    const EVENT_SOMMERCE_ORDER_COMPLETED = 'sommerce_order_completed';
    const EVENT_SOMMERCE_ABANDONED_CHECKOUT = 'sommerce_abandoned_checkout';

    const EVENT_SOMMERCE_NEW_ORDER = 'sommerce_new_order';
    const EVENT_SOMMERCE_ORDER_FAIL = 'sommerce_order_fail';
    const EVENT_SOMMERCE_ORDER_ERROR = 'sommerce_order_error';

    /**
     * Add custom event
     * @param $name
     * @param $data
     */
    public static function add($name, $data)
    {
        Event::trigger(static::class, $name, new Event([
            'sender' => $data,
        ]));
    }
}