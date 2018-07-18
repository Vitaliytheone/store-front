<?php
namespace console\controllers\my;

use my\components\scanners\components\info\RentapanelInfo;
use my\components\scanners\Scanner;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

/**
 * Class PanelScannerController
 * @package console\controllers\my
 */
class PanelScannerController extends CustomController
{
    /** @inheritdoc */
    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        Yii::$app->db->commandClass = '\my\components\db\Command';
        Yii::$app->db->commandMap = array_merge(Yii::$app->db->commandMap, [
            'mysqli' => '\my\components\db\Command',
            'mysql' => '\my\components\db\Command'
        ]);

        $before = Yii::$app->db->createCommand('SHOW VARIABLES LIKE \'%timeout%\'')->queryAll();
        $this->stderr('Before set timeout: ' .  print_r($before,1) . "\n", Console::FG_GREEN);


        Yii::$app->db->createCommand('SET SESSION wait_timeout = 28800;')->execute();
        Yii::$app->db->createCommand('SET SESSION interactive_timeout = 28800;')->execute();


        $after = Yii::$app->db->createCommand('SHOW VARIABLES LIKE \'%timeout%\'')->queryAll();
        $this->stderr('After set timeout: ' .  print_r($after,1) . "\n", Console::FG_YELLOW);
    }

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