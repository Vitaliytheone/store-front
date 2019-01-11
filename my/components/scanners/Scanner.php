<?php

namespace my\components\scanners;

use my\components\scanners\components\BaseScanner;
use yii\base\UnknownClassException;

/**
 * Class Scanner
 * @package my\components\scanners
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
        $className = 'my\components\scanners\components\scanners\\' . ucfirst($scannerName) . 'Scanner';

        if (!class_exists($className)) {
            return null;
        }

        return new $className($config);
    }
}
