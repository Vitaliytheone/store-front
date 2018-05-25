<?php
use common\events\Events;
use common\events\store\OrderCreatedEvent;
use yii\base\Event;

Event::on(
    Events::class,
    Events::EVENT_STORE_ORDER_CONFIRM,
    function ($event) {
        /**
         * @var array $sender
         */
        $sender = $event->sender;

        Yii::$container->get(OrderCreatedEvent::class, [
            $sender['store'],
            $sender['order'],
        ])->run();
    }
);