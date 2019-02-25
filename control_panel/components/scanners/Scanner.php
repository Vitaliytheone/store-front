<?php

namespace control_panel\components\scanners;

use control_panel\components\scanners\components\BaseScanner;
use yii\base\UnknownClassException;

/**
 * Class Scanner
 * @package control_panel\components\scanners
 */
class Scanner
{
    /**
     * @param $scannerName
     * @param $config
     * @return BaseScanner
     * @throws UnknownClassException
     */
    public static function getScanner($scannerName, $config)
    {
        $className = 'control_panel\components\scanners\components\scanners\\' . ucfirst($scannerName) . 'Scanner';

        if (!class_exists($className)) {
            return null;
        }

        return new $className($config);
    }
}
