<?php
namespace common\helpers;

use ReflectionClass;
use common\models\panels\Project;
use common\models\stores\Stores;
use Yii;
use common\models\panels\ThirdPartyLog;
use Exception;

/**
 * Class NginxHelper
 * @package common\helpers
 */
class NginxHelper {

    /**
     * Create nginx config file by object
     * @param Project|Stores $object
     * @return bool
     */
    public static function create($object)
    {
        switch ((new ReflectionClass($object))->getShortName()) {
            case 'Project':
                /**
                 * @var Project $object
                 */
                $domain = $object->site;
                $logItem = ThirdPartyLog::ITEM_BUY_PANEL;
                $logCode = 'project.create_nginx_config';
                $configPath = Yii::$app->params['panelNginxConfigPath'];
                $defaultConfigPath = Yii::$app->params['panelNginxDefaultConfigPath'];
            break;

            case 'Stores':
                /**
                 * @var Stores $object
                 */
                $domain = $object->domain;
                $logItem = ThirdPartyLog::ITEM_BUY_STORE;
                $logCode = 'store.create_nginx_config';
                $configPath = Yii::$app->params['storeNginxConfigPath'];
                $defaultConfigPath = Yii::$app->params['storeNginxDefaultConfigPath'];
            break;

            default:
                throw new Exception();
        }

        $subPrefix = str_replace('.', '-', $domain);
        $configPath = rtrim($configPath, '/') . '/';
        $configPath = $configPath . $subPrefix . '.conf';

        // Create nginx config
        if (!file_exists($configPath)) {
            if (file_exists($defaultConfigPath)) {
                $configContent = file_get_contents($defaultConfigPath);
                $configContent = str_replace('domain_name', $domain, $configContent);
                @file_put_contents($configPath, $configContent);
            }
        }

        if (!file_exists($configPath)) {
            ThirdPartyLog::log($logItem, $object->id, '', $logCode);
            return false;
        }

        return true;
    }

    /**
     * Delete nginx config file by object
     * @param Project|Stores $object
     * @return bool
     */
    public static function delete($object)
    {
        switch ((new ReflectionClass($object))->getShortName()) {
            case 'Project':
                /**
                 * @var Project $object
                 */
                $domain = $object->site;
                $logItem = ThirdPartyLog::ITEM_BUY_PANEL;
                $logCode = 'project.remove_nginx_config';
                $configPath = Yii::$app->params['panelNginxConfigPath'];
            break;

            case 'Stores':
                /**
                 * @var Stores $object
                 */
                $domain = $object->domain;
                $logItem = ThirdPartyLog::ITEM_BUY_STORE;
                $logCode = 'store.remove_nginx_config';
                $configPath = Yii::$app->params['storeNginxConfigPath'];
            break;

            default:
                throw new Exception();
        }

        $subPrefix = str_replace('.', '-', $domain);
        $configPath = rtrim($configPath, '/') . '/';

        // Remove nginx config
        if (file_exists($configPath . $subPrefix . '.conf')) {
            unlink($configPath . $subPrefix . '.conf');
        }

        if (file_exists($configPath . $subPrefix . '.conf')) {
            ThirdPartyLog::log($logItem, $object->id, '', $logCode);
            return false;
        }

        return true;
    }
}