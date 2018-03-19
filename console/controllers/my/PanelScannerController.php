<?php
namespace console\controllers\my;

use my\components\scanners\Scanner;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class PanelScannerController
 * @package console\controllers\my
 */
class PanelScannerController extends CustomController
{
    /**
     * Return
     * @return array
     */
    private static function _commonConfig()
    {
        return [
            'apiKey' => ArrayHelper::getValue(Yii::$app->params, 'levopanel_scanner.apiKey'),
            'proxy' => ArrayHelper::getValue(Yii::$app->params, 'levopanel_scanner.proxy'),
            'timeouts' => ArrayHelper::getValue(Yii::$app->params, 'levopanel_scanner.timeouts'),
        ];
    }

    /**
     * Fetch and research statuses of new domains
     * @param $scannerName string
     */
    public function actionScanNew($scannerName)
    {
        $scanner = Scanner::getScanner($scannerName, static::_commonConfig());

        if (empty($scanner)) {
            exit( PHP_EOL . 'No scanner found for ' . $scannerName . PHP_EOL);
        }

        $scanner->researchNewDomains();
    }

    /**
     * Research statuses of all domains in db
     * @param $scannerName string
     */
    public function actionCheckAll($scannerName)
    {
        $scanner = Scanner::getScanner($scannerName, static::_commonConfig());

        if (empty($scanner)) {
            exit(PHP_EOL . 'No scanner found for ' . $scannerName . PHP_EOL);
        }

        $scanner->researchAllDomains();
    }
}