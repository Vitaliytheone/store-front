<?php
namespace common\super_tasks;

/**
 * Class BaseTask
 * @package common\super_tasks
 */
abstract class BaseTask {

    /**
     * @var int
     */
    public $task;

    /**
     * @var array
     */
    public $options;

    /**
     * BaseTask constructor.
     * @param int $task
     * @param array $options
     */
    public function __construct(int $task = null, array $options = [])
    {
        $this->task = $task;
        $this->options = $options;
    }

    /**
     * @return mixed
     */
    public abstract function run(): void;
}