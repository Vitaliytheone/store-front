<?php

namespace console\components\crons;

use yii\base\Component;
use yii\console\Controller;

/**
 * Class BaseCron
 * @package console\components\crons
 */
abstract class CronBase extends Component
{
    /**
     * Current console
     * @var Controller
     */
    private $_console;

    /**
     * Debug on/off
     * @var boolean
     */
    private $_debug = false;

    /**
     * Return cron task name
     * @return string
     */
    public function cronTaskName()
    {
        return get_class($this);
    }

    /**
     * Set console
     * @param Controller $console
     */
    public function setConsole(Controller $console)
    {
        $this->_console = $console;
    }

    /**
     * Get console
     * @return Controller
     */
    public function getConsole()
    {
        return $this->_console;
    }

    /**
     * Set debug
     * @param bool $debug
     */
    public function setDebug(bool $debug)
    {
        $this->_debug = $debug;
    }

    /**
     * Get debug
     * @return bool
     */
    public function getDebug() : bool
    {
       return $this->_debug;
    }

    /**
     * Write message to std out
     * @param string $message
     * @param int $color
     */
    protected function stdout(string $message, int $color = null)
    {
        if (!$this->_debug) {
            return;
        }

        $this->_console->stdout($message . PHP_EOL,  $color);
    }

    /**
     * Write error to stdout
     * @param string $message
     */
    protected function stderror(string $message)
    {
        if (!$this->_debug) {
            return;
        }

        $this->_console->stderr($message . PHP_EOL);
    }

    /**
     * Run cron task
     * @return mixed
     */
    abstract function run();
}