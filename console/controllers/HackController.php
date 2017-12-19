<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use console\components\panelchecker\PanelcheckerComponent;

/**
 * Hacks and research controller
 * @package console\controllers
 */
class HackController extends Controller
{
    /**
     * Levopanel checker
     * @return int
     */
    public function actionLevochecker()
    {
        $config = [
            'class' => PanelcheckerComponent::className(),
            'apiKey' => 'b9f1d6f809b793321c700f45ca382f59ef83bf644c48118e6d3b9902ab0cb86f',
            'apiVersion' => '1.0',
            'db' => [
                'name' => 'checker',
                'table' => 'levopanel',
            ],
            'dbFields' => [
                'domains_column' => 'domain',
                'status_column' => 'status',
                'updated_column' => 'updated_at',
                'created_column' => 'created_at',
                'ip_column' => 'server_ip',
                'details_column' => 'details',
            ],
            'proxy' => [
                'ip',
                'port',
                'type' => CURLPROXY_HTTP,
            ],
        ];

        /** @var \console\components\panelchecker\PanelcheckerComponent $checker */
        $checker = Yii::createObject($config);
        $checker->check();

        return Controller::EXIT_CODE_NORMAL;
    }

    /**
     * Only for test purpose
     * @return int
     */
    public function actionTest()
    {
        $networks = [
            '147.135.223.128',
            '104.*.*.*',
        ];

        $res = PanelcheckerComponent::matchNetwork('104.12.1.0', '244.12.2.2');
        // $res = PanelcheckerComponent::matchNetworks('104.31.79.202', $networks);

        echo ($res ? "YES" : 'NO') . PHP_EOL;

        return Controller::EXIT_CODE_NORMAL;
    }


}
