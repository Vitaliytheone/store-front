<?php

namespace control_panel\models\search;


use control_panel\helpers\DomainsHelper;
use Yii;
use common\models\gateways\Sites;
use common\models\panels\Customers;
use common\models\panels\Orders;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class GatewaysSearch
 * @package control_panel\models\search
 */
class GatewaysSearch
{
    /**
     * @var Customers
     */
    private $customer;

    /**
     * Set search parameters
     * @param Customers $customer
     */
    public function setCustomer(Customers $customer)
    {
        $this->customer = $customer;
    }

    /**
     * Build sql query
     * @return array
     */
    public function buildQuery()
    {
        $customerId = $this->customer->id;

        $orderPending = (new Query())
            ->select(['id', '("order") AS type', 'domain AS domain', 'status', 'date', '(NULL) AS expired', '(NULL) AS db', 'hide'])
            ->from('orders')
            ->andWhere([
                'cid' => $customerId,
                'status' => [
                    Orders::STATUS_PAID,
                    Orders::STATUS_PENDING,
                    Orders::STATUS_ERROR
                ],
                'item' => Orders::ITEM_BUY_GATEWAY,
            ])->orderBy([
                'id' => SORT_ASC
            ]);

        $orderCanceled = (new Query())
            ->select(['id', '("order") AS type', 'domain AS domain', 'status', 'date', '(NULL) AS expired', '(NULL) AS db', 'hide'])
            ->from('orders')
            ->andWhere([
                'cid' => $customerId,
                'status' => Orders::STATUS_CANCELED,
                'item' => Orders::ITEM_BUY_GATEWAY
            ])->orderBy([
                'id' => SORT_ASC
            ]);

        $gateways = (new Query())
            ->select(['id', '("gateway") AS type', 'domain', 'status', 'created_at AS date', 'expired_at', 'db_name AS db', '(NULL) AS hide'])
            ->from(DB_GATEWAYS . '.sites')
            ->andWhere([
                'customer_id' => $customerId,
                'status' => [
                    Sites::STATUS_ACTIVE,
                    Sites::STATUS_FROZEN,
                    Sites::STATUS_TERMINATED,
                ]
            ])
            ->orderBy([
                new Expression('FIELD (status, ' . implode(',', [
                        Sites::STATUS_ACTIVE,
                        Sites::STATUS_FROZEN,
                        Sites::STATUS_TERMINATED,
                    ]) . ')'),
                'id' => SORT_ASC
            ]);

        return [
            'pending' => $orderPending,
            'gateways' => $gateways,
            'canceled' => $orderCanceled
        ];
    }

    /**
     * Search panels
     * @return array
     */
    public function search()
    {
        $timezone = null;
        if (!Yii::$app->user->isGuest) {
            $timezone = $this->customer->timezone;
        }

        $errorsOrders = ArrayHelper::getColumn(Orders::find()->andWhere([
            'status' => Orders::STATUS_ERROR,
            'item' => Orders::ITEM_BUY_GATEWAY
        ])->all(), 'domain');

        $ordersStatuses = Orders::getStatuses();
        $gatewayStatuses = Sites::getStatuses();

        $prepareData = function ($value, $code) use ($ordersStatuses, $gatewayStatuses, $timezone, $errorsOrders) {
            $access = [
                'isActive' => false,
            ];
            if ('pending' == $code) {
                $value['statusName'] = $ordersStatuses[Orders::STATUS_PENDING];
            } else if ('canceled' == $code) {
                $value['statusName'] = $ordersStatuses[Orders::STATUS_CANCELED];
            } else {
                // skip gateways with errors
                if (in_array($value['domain'], $errorsOrders)) {
                    return null;
                }
                $value['statusName'] = $gatewayStatuses[$value['status']];
                $value['expired'] = Yii::$app->formatter->asDate($value['expired_at'] + ((int)$timezone), 'php:Y-m-d H:i:s');

                $access['isActive'] = Sites::hasAccess($value, Sites::CAN_DASHBOARD);
            }

            $value['date'] = Yii::$app->formatter->asDate($value['date'] + ((int)$timezone), 'php:Y-m-d H:i:s');
            $value['domain'] = DomainsHelper::idnToUtf8($value['domain']);
            $value['access'] = $access;
            //$value['hide'] = ArrayHelper::getValue($value, 'hide', null);

            return $value;
        };

        $queries = $this->buildQuery();

        $return = [];

        foreach ($queries as $code => $query) {
            $data = $query->all();

            foreach ($data as $value) {
                $value = $prepareData($value, $code);

                if (!empty($value)) {
                    $return[] = $value;
                }
            }
        }

        return $return;
    }
}