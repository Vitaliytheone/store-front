<?php

namespace console\helpers;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class ConsoleHelper
 * Helpers for console commands
 * @package console\helpers
 */
class ConsoleHelper
{
    /**
     * Execute Yii console command
     *
     * Example:
     * execConsoleCommand('system-sommerce/generate-assets');
     *
     * @param $command
     * @param array $output contains array of console command result strings
     * @return bool True if execution success, false otherwise
     */
    public static function execConsoleCommand($command, &$output = [])
    {
        $cli = ArrayHelper::getValue(Yii::$app->params, 'php_cli_path', 'php');

        $cmd = $cli . ' ' . Yii::getAlias('@project_root') . '/yii ' . ' ' . $command . ' 2>&1';

        exec($cmd, $output, $returnVar);

        Yii::debug(print_r($cli,1), 'my_debug');
        Yii::debug(print_r($cmd,1), 'my_debug');
        Yii::debug(print_r($output,1), 'my_debug');

        return (int)$returnVar === 0;
    }
}