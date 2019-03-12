<?php
use store\events\Events;
use store\events\handlers\OrderCreatedEvent;
use store\events\handlers\OrderErrorEvent;
use store\events\handlers\OrderFailEvent;
use store\events\handlers\OrderAbandonedEvent;
use store\events\handlers\OrderCompletedEvent;
use store\events\handlers\OrderInProgressEvent;
use store\events\handlers\OrderChangedStatusEvent;
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

        Yii::$container->get(OrderFailEvent::class, [
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
            $sender['store'],
        ])->run();
    }
);

Event::on(
    Events::class,
    Events::EVENT_STORE_ORDER_IN_PROGRESS,
    function ($event) {
        /**
         * @var array $sender
         */
        $sender = $event->sender;

        Yii::$container->get(OrderInProgressEvent::class, [
            $sender['storeId'],
            $sender['suborderId'],
        ])->run();
    }
);

Event::on(
    Events::class,
    Events::EVENT_STORE_ORDER_COMPLETED,
    function ($event) {
        /**
         * @var array $sender
         */
        $sender = $event->sender;

        Yii::$container->get(OrderCompletedEvent::class, [
            $sender['storeId'],
            $sender['suborderId'],
        ])->run();
    }
);

Event::on(
    Events::class,
    Events::EVENT_STORE_ORDER_CHANGED_STATUS,
    function ($event) {
        /**
         * @var array $sender
         */
        $sender = $event->sender;

        Yii::$container->get(OrderChangedStatusEvent::class, [
            $sender['storeId'],
            $sender['suborderId'],
            $sender['status']
        ])->run();
    }
);