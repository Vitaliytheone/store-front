<?php
namespace common\tasks\workers;

/**
 * Class TestWorker
 * @package app\tasks\workers
 */
abstract class BaseWorker {

    /**
     * @var string|null
     */
    protected $_error;

    /**
     * @var mixed
     */
    protected $_data;

    /**
     * TestWorker constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->_data = $data;
    }

    /**
     * @return boolean
     */
    abstract public function run(): bool;

    /**
     * @return string|null
     */
    public function getError()
    {
        return $this->_error;
    }
}