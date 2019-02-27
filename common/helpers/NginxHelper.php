<?php

namespace common\helpers;

use common\models\gateways\Sites;
use ReflectionClass;
use common\models\panels\Project;
use common\models\stores\Stores;
use Yii;
use common\models\panels\ThirdPartyLog;
use Exception;
use common\models\sommerces\Stores as Sommerce;

/**
 * Class NginxHelper
 * @package common\helpers
 */
class NginxHelper
{
    /**
     * Create nginx config file by object
     * @param Project|Stores|Sites|Sommerce $object
     * @param bool $isSommerce
     * @return bool
     * @throws \ReflectionException
     * @throws \Exception
     */
    public static function create($object, $isSommerce = false)
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
                 * @var Stores|Sommerce $object
                 */
                $domain = $object->domain;
                $logItem = ThirdPartyLog::ITEM_BUY_STORE;
                $logCode = 'store.create_nginx_config';
                if ($isSommerce) {
                    $configPath = Yii::$app->params['sommerceNginxConfigPath'];
                    $defaultConfigPath = Yii::$app->params['sommerceNginxDefaultConfigPath'];
                } else {
                    $configPath = Yii::$app->params['storeNginxConfigPath'];
                    $defaultConfigPath = Yii::$app->params['storeNginxDefaultConfigPath'];
                }
            break;

            case 'Sites':
                /**
                 * @var Sites $object
                 */
                $domain = $object->domain;
                $logItem = ThirdPartyLog::ITEM_BUY_GATEWAY;
                $logCode = 'gateway.create_nginx_config';
                $configPath = Yii::$app->params['gatewayNginxConfigPath'];
                $defaultConfigPath = Yii::$app->params['gatewayNginxDefaultConfigPath'];
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
     * @param Project|Stores|Sites|Sommerce $object
     * @param bool $isSommerce
     * @return bool
     * @throws \ReflectionException
     * @throws \Exception
     */
    public static function delete($object, $isSommerce = false)
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
                 * @var Stores|Sommerce $object
                 */
                $domain = $object->domain;
                $logItem = ThirdPartyLog::ITEM_BUY_STORE;
                $logCode = 'store.remove_nginx_config';
                if ($isSommerce) {
                    $configPath = Yii::$app->params['sommerceNginxConfigPath'];
                } else {
                    $configPath = Yii::$app->params['storeNginxConfigPath'];
                }
            break;

            case 'Sites':
                /**
                 * @var Sites $object
                 */
                $domain = $object->domain;
                $logItem = ThirdPartyLog::ITEM_BUY_GATEWAY;
                $logCode = 'gateway.remove_nginx_config';
                $configPath = Yii::$app->params['gatewayNginxConfigPath'];
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