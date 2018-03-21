<?php

namespace my\models\search;

use my\helpers\DomainsHelper;
use common\models\panels\Domains;
use Yii;
use common\models\panels\Orders;
use common\models\panels\Project;
use common\models\panels\Tariff;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\db\Expression;
use yii\helpers\Json;

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
                    Orders::STATUS_PENDING
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
                $value['expired'] = Yii::$app->formatter->asDate($value['expired'] + ((int)$timezone) + Yii::$app->params['time'], 'php:Y-m-d H:i:s');
            }

            $value['date'] = Yii::$app->formatter->asDate($value['date'] + ((int)$timezone) + Yii::$app->params['time'], 'php:Y-m-d H:i:s');
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
}