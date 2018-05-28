<?php
use common\events\Events;
use common\events\store\OrderCreatedEvent;
use common\events\store\OrderErrorEvent;
use common\events\store\OrderAbandonedEvent;
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

Event::on(
    Events::class,
    Events::EVENT_STORE_ORDER_ERROR,
    function ($event) {
        /**
         * @var array $sender
         */
        $sender = $event->sender;

        Yii::$container->get(OrderErrorEvent::class, [
            $sender['storeId'],
            $sender['suborderId'],
        ])->run();
    }
);

Event::on(
    Events::class,
    Events::EVENT_STORE_ORDER_FAIL,
    function ($event) {
        /**
         * @var array $sender
         */
        $sender = $event->sender;

        Yii::$container->get(OrderErrorEvent::class, [
            $sender['storeId'],
            $sender['suborderId'],
        ])->run();
    }
);

Event::on(
    Events::class,
    Events::EVENT_STORE_ABANDONED_CHECKOUT,
    function ($event) {
        /**
         * @var array $sender
         */
        $sender = $event->sender;

        Yii::$container->get(OrderAbandonedEvent::class, [
            $sender['checkout'],
        ])->run();
    }
);