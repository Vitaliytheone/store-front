<?php

namespace common\tasks\workers;

/**
 * Class TestWorker
 * @package app\tasks\workers
 */
abstract class BaseWorker {
    abstract public static function run($data);
}