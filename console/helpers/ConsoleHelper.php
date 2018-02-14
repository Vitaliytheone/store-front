<?php

namespace console\helpers;

use Yii;

/**
 * Class ConsoleHelper
 * Helpers for console commands
 * @package console\helpers
 */
class ConsoleHelper
{
    /**
     * Execute generate assets script
     * @return string
     */
    public static function execGenerateAssets() {
        return shell_exec('php ' . Yii::getAlias('@project_root') . '/yii ' . 'system/generate-assets');
    }
}