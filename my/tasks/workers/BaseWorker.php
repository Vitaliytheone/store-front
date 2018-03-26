<?php

namespace my\tasks\workers;

/**
 * Class TestWorker
 * @package my\tasks\workers
 */
abstract class BaseWorker {
    abstract public static function run($data);
}