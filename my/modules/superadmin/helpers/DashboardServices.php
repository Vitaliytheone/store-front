<?php
namespace superadmin\helpers;

use common\models\panels\Params;
use superadmin\components\services\AHnamesService;
use superadmin\components\services\BaseService;
use superadmin\components\services\DNSLyticsService;
use superadmin\models\search\dashboard\DashboardService;
use superadmin\components\services\GoGetSSLService;
use superadmin\components\services\OpenSRSService;
use superadmin\components\services\WhoisxmlService;
use ReflectionClass;
use Yii;

/**
 * Class DashboardServices
 * Helper class class for receiving dashboard services
 * @package superadmin\helpers
 */
class DashboardServices
{
    const SERVICE_WHOISXML = 'whoisxmlapi';
    const SERVICE_AHNAMES = 'ahnames';
    const SERVICE_GOGETSSL = 'ggssl';
    const SERVICE_OPENSRS = 'opensrs';
    const SERVICE_DNSLYTICS = 'dnslytics';

    private static function _getConfig()
    {
        $whoisxmlParams = Params::get('service.whoisxml');
        $ahnamesParams = Params::get('service.ahnames');
        $gogetsslParams = Params::get('service.gogetssl');
        $dnslyticsParams = Params::get('service.dnslytics');
        $opensrsParams = Params::get('service.opensrs');

        return [
            self::SERVICE_WHOISXML => [
                'class' => WhoisxmlService::class,
                'name' => Yii::t('app/superadmin', 'dashboard.services.whoisapi'),
                'params' => [
                    $whoisxmlParams['whoisxml.url'],
                    $whoisxmlParams['dnsLogin'],
                    $whoisxmlParams['dnsPasswd'],
                    Yii::$app->params['curl.timeout']
                ]
            ],
            self::SERVICE_AHNAMES => [
                'class' => AHnamesService::class,
                'name' => Yii::t('app/superadmin', 'dashboard.services.ahnames'),
                'params' => [
                    $ahnamesParams['ahnames.url'],
                    $ahnamesParams['ahnames.login'],
                    $ahnamesParams['ahnames.password'],
                    Yii::$app->params['curl.timeout']
                ]
            ],
            self::SERVICE_GOGETSSL => [
                'class' => GoGetSSLService::class,
                'name' => Yii::t('app/superadmin', 'dashboard.services.ggssl'),
                'params' => [
                    $gogetsslParams['goGetSSLUsername'],
                    $gogetsslParams['goGetSSLPassword'],
                    Yii::$app->params['curl.timeout'],
                    $gogetsslParams['testSSL']
                ]
            ],
            self::SERVICE_OPENSRS => [
                'class' => OpenSRSService::class,
                'name' => Yii::t('app/superadmin', 'dashboard.services.opensrs'),
                'params' => [
                    $opensrsParams['openSRS.ip']
                ]
            ],
            self::SERVICE_DNSLYTICS => [
                'class' => DNSLyticsService::class,
                'name' => Yii::t('app/superadmin', 'dashboard.services.dnslytics'),
                'params' => [
                    $dnslyticsParams['dnslytics.apiKey'],
                    $dnslyticsParams['dnslytics.url'],
                    Yii::$app->params['curl.timeout'],
                ]
            ]
        ];
    }

    /**
     * @param $serviceName
     * @return DashboardService
     */
    public static function getService($serviceName)
    {
        $configServices = static::_getConfig();
        if (empty($configServices[$serviceName])) {
            return null;
        }
        $class = $configServices[$serviceName]['class'];
        $args = $configServices[$serviceName]['params'];
        /** @var BaseService $source*/
        $source = (new ReflectionClass($class))->newInstanceArgs($args);
        return new DashboardService($source, $configServices[$serviceName]['name']);
    }

    /**
     * Get services
     * @return array
     */
    public static function getServices() {
        $services = [];
        $configServices = static::_getConfig();
        foreach ($configServices as $key => $service) {
            $services[$key] = static::getService($key);
        }

        return $services;
    }

}