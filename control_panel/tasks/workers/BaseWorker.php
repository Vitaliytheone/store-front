<?php

namespace control_panel\tasks\workers;

/**
 * Class TestWorker
 * @package control_panel\tasks\workers
 */
abstract class BaseWorker {
    abstract public static function run($data);
}