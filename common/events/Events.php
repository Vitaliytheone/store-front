<?php
namespace common\events;
use yii\base\Event;

/**
 * Class Events
 * @package common\events
 */
class Events {
    const EVENT_STORE_ORDER_CHANGED_STATUS = 'store_order_changed_status';

    const EVENT_STORE_ORDER_CONFIRM = 'store_order_confirm';
    const EVENT_STORE_ORDER_IN_PROGRESS = 'store_order_in_progress';
    const EVENT_STORE_ORDER_COMPLETED = 'store_order_completed';
    const EVENT_STORE_ABANDONED_CHECKOUT = 'store_abandoned_checkout';

    const EVENT_STORE_NEW_ORDER = 'store_new_order';
    const EVENT_STORE_ORDER_FAIL = 'store_order_fail';
    const EVENT_STORE_ORDER_ERROR = 'store_order_error';

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