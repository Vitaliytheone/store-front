<?php

namespace my\models\search;

use common\components\domains\BaseDomain;
use common\components\domains\Domain;
use common\models\panels\DomainZones;
use my\helpers\DomainsHelper;
use common\models\panels\Domains;
use Yii;
use common\models\panels\Orders;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\db\Expression;

/**
 * Class DomainsSearch
 * @package my\models\search
 */
class DomainsSearch
{
    private $params;

    /**
     * Set search parameters
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * Build sql query
     * @return $this
     */
    public function buildQuery()
    {
        $customer = ArrayHelper::getValue($this->params, 'customer_id');

        $orderPending = (new Query())
            ->select(['id', '("order") AS type', 'domain', 'status', 'date', '(NULL) AS expired'])
            ->from('orders')
            ->andWhere([
                'cid' => $customer,
                'status' => [
                    Orders::STATUS_PAID,
                    Orders::STATUS_PENDING,
                    Orders::STATUS_ERROR
                ],
                'item' => Orders::ITEM_BUY_DOMAIN,
            ])->orderBy([
                'id' => SORT_ASC
            ]);

        $orderCanceled = (new Query())
            ->select(['id', '("order") AS type', 'domain', 'status', 'date', '(NULL) AS expired'])
            ->from('orders')
            ->andWhere([
                'cid' => $customer,
                'status' => Orders::STATUS_CANCELED,
                'item' => Orders::ITEM_BUY_DOMAIN
            ])->orderBy([
                'id' => SORT_ASC
            ]);

        $domains = (new Query())
            ->select(['id', '("domain") AS type', 'domain', 'status', 'created_at AS date', 'expiry AS expired'])
            ->from('domains')
            ->andWhere([
                'customer_id' => $customer,
                'status' => [
                    Domains::STATUS_OK,
                    Domains::STATUS_EXPIRED,
                ]
            ])
            ->orderBy([
                new Expression('FIELD (status, ' . implode(',', [
                        Domains::STATUS_OK,
                        Domains::STATUS_EXPIRED,
                    ]) . ')'),
                'id' => SORT_ASC
            ]);

        return [
            'pending' => $orderPending,
            'domains' => $domains,
            'canceled' => $orderCanceled
        ];
    }

    /**
     * Search domains
     * @return array
     */
    public function search()
    {
        $timezone = null;
        if (!Yii::$app->user->isGuest) {
            $timezone = Yii::$app->user->identity->timezone;
        }

        $ordersStatuses = Orders::getStatuses();
        $domainStatuses = Domains::getStatuses();

        $prepareData = function ($value, $code) use ($ordersStatuses, $domainStatuses, $timezone) {
            if ('pending' == $code) {
                $value['statusName'] = $ordersStatuses[Orders::STATUS_PENDING];
            } else if ('canceled' == $code) {
                $value['statusName'] = $ordersStatuses[Orders::STATUS_CANCELED];
            } else {
                $value['statusName'] = $domainStatuses[$value['status']];
                $value['expired'] = Yii::$app->formatter->asDate($value['expired'] + ((int)$timezone), 'php:Y-m-d H:i:s');
            }

            $value['date'] = Yii::$app->formatter->asDate($value['date'] + ((int)$timezone), 'php:Y-m-d H:i:s');
            $value['domain'] = DomainsHelper::idnToUtf8($value['domain']);
            return $value;
        };

        $queries = $this->buildQuery();

        $return = [];

        foreach ($queries as $code => $query) {
            $data = $query->all();

            foreach ($data as $value) {
                $return[] = $prepareData($value, $code);
            }
        }

        return $return;
    }

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
        $result = [];

        if (false !== strpos($paramDomain, '.')) {
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
                if ($zone->registrar === strtolower($registrar)) {
                    $registrarDomains[$id] = mb_strtolower($paramDomain . $zone->zone);
                }
            }

            /** @var BaseDomain $registrar */

            $registrar = Domain::createRegistrarClass($registrar);
            $result += $registrar::domainsCheck(array_map([new DomainsHelper, 'idnToAscii'], $registrarDomains));
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

        $return = [];

        foreach ($domains as $id => $domain) {
            if (!isset($result[$domain])) {
                continue;
            }

            $return[] = [
                'zone' => $id,
                'domain' => $domain,
                'price' => $zones[$id]->price_register,
                'is_available' => $result[$domain] && !in_array($domain, $existsDomains)
            ];
        }

        return $return;
    }
}