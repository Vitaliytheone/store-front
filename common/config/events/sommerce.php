<?php
use sommerce\events\Events;
use sommerce\events\handlers\OrderCreatedEvent;
use sommerce\events\handlers\OrderErrorEvent;
use sommerce\events\handlers\OrderFailEvent;
use sommerce\events\handlers\OrderAbandonedEvent;
use sommerce\events\handlers\OrderCompletedEvent;
use sommerce\events\handlers\OrderInProgressEvent;
use sommerce\events\handlers\OrderChangedStatusEvent;
use yii\base\Event;

Event::on(
    Events::class,
    Events::EVENT_SOMMERCE_ORDER_CONFIRM,
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
    Events::EVENT_SOMMERCE_ORDER_ERROR,
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
    Events::EVENT_SOMMERCE_ORDER_FAIL,
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
    Events::EVENT_SOMMERCE_ABANDONED_CHECKOUT,
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
    Events::EVENT_SOMMERCE_ORDER_IN_PROGRESS,
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
    Events::EVENT_SOMMERCE_ORDER_COMPLETED,
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
    Events::EVENT_SOMMERCE_ORDER_CHANGED_STATUS,
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