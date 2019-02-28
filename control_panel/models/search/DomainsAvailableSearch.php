<?php

namespace control_panel\models\search;

use common\components\domains\BaseDomain;
use common\components\domains\Domain;
use common\models\sommerces\DomainZones;
use control_panel\helpers\DomainsHelper;
use Yii;
use common\models\sommerces\Orders;
use yii\helpers\ArrayHelper;

/**
 * Class DomainsAvailableSearch
 * @package control_panel\models\search
 */
class DomainsAvailableSearch
{


    /**
     * Searches for available domains for registration
     * @param string $paramDomain
     * @param string $paramZone
     * @return array
     * @throws yii\base\UnknownClassException
     */
    public function searchDomains($paramDomain, $paramZone)
    {
        $zones = ArrayHelper::index(DomainZones::find()->all(), 'id');
        $registrars = DomainsHelper::getAllRegistrars();
        $result = $return = $convertedResult = [];

        if (false !== mb_stripos($paramDomain, '.')) {
            $paramDomain = explode('.', $paramDomain)[0];
        }

        $domains = [
            $paramZone => ''
        ];

        foreach ($zones as $id => $zone) {
            $domains[$id] = mb_strtolower($paramDomain . $zone->zone);
        }


        foreach ($registrars as $registrar) {
            $registrarDomains = [];
            foreach ($zones as $id => $zone) {
                /** @var DomainZones $zone */
                if ($zone->registrar === mb_strtolower($registrar)) {
                    $registrarDomains[$id] = mb_strtolower($paramDomain . $zone->zone);
                }
            }

            /** @var BaseDomain $registrar */
            $registrar = Domain::createRegistrarClass($registrar);
            $result += $registrar::domainsCheck(array_map([new DomainsHelper(), 'idnToAscii'], $registrarDomains));
        }

        $existsDomains = Orders::find()->andWhere([
            'domain' => array_keys($result),
            'item' => Orders::ITEM_BUY_DOMAIN,
            'status' => [
                Orders::STATUS_PENDING,
                Orders::STATUS_PAID,
                Orders::STATUS_ADDED,
                Orders::STATUS_ERROR
            ]
        ])->all();
        $existsDomains = ArrayHelper::getColumn($existsDomains, 'domain');


        foreach ($result as $key => $value) {
            $key = DomainsHelper::idnToAscii($key);
            $convertedResult[$key] = $value;
        }

        foreach ($domains as $id => $domain) {
            $domainAscii = DomainsHelper::idnToAscii($domain);
            if (!isset($convertedResult[$domainAscii])) {
                continue;
            }

            $return[] = [
                'zone' => $id,
                'domain' => $domain,
                'price' => $zones[$id]->price_register,
                'is_available' => $convertedResult[$domainAscii] && !in_array($domainAscii, $existsDomains)
            ];
        }

        return $return;
    }
}