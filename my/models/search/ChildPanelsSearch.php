<?php

namespace my\models\search;

use my\helpers\ChildHelper;
use my\helpers\DomainsHelper;
use Yii;
use common\models\panels\Orders;
use common\models\panels\Project;
use common\models\panels\Tariff;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\db\Expression;
use yii\helpers\Json;

/**
 * Class ChildPanelsSearch
 * @package my\models\search
 */
class ChildPanelsSearch
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
     * @return array
     */
    public function buildQuery()
    {
        $customer = ArrayHelper::getValue($this->params, 'customer_id');

        $orderPending = (new Query())
            ->select(['id', '("order") AS type', 'domain AS domain', 'status', 'date', 'details AS provider', '(NULL) AS expired', '(NULL) AS db'])
            ->from('orders')
            ->andWhere([
                'cid' => $customer,
                'status' => [
                    Orders::STATUS_PAID,
                    Orders::STATUS_PENDING,
                    Orders::STATUS_ERROR
                ],
                'item' => Orders::ITEM_BUY_CHILD_PANEL,
            ])->orderBy([
                'id' => SORT_ASC
            ]);

        $orderCanceled = (new Query())
            ->select(['id', '("order") AS type', 'domain AS domain', 'status', 'date', 'details AS provider', '(NULL) AS expired', '(NULL) AS db'])
            ->from('orders')
            ->andWhere([
                'cid' => $customer,
                'status' => Orders::STATUS_CANCELED,
                'item' => Orders::ITEM_BUY_CHILD_PANEL
            ])->orderBy([
                'id' => SORT_ASC
            ]);

        $projects = (new Query())
            ->select(['id', '("project") AS type', 'site as domain', 'act AS status', 'date', 'provider_id AS provider', 'expired', 'db'])
            ->from('project')
            ->andWhere([
                'cid' => $customer,
                'child_panel' => 1,
                'act' => [
                    Project::STATUS_ACTIVE,
                    Project::STATUS_FROZEN,
                    Project::STATUS_TERMINATED,
                ]
            ])
            ->orderBy([
                new Expression('FIELD (act, ' . implode(',', [
                        Project::STATUS_ACTIVE,
                        Project::STATUS_FROZEN,
                        Project::STATUS_TERMINATED,
                    ]) . ')'),
                'id' => SORT_ASC
            ]);

        return [
            'pending' => $orderPending,
            'projects' => $projects,
            'canceled' => $orderCanceled
        ];
    }

    /**
     * Get providers
     * @return array
     */
    public function getProviders()
    {
        $customer = ArrayHelper::getValue($this->params, 'customer_id');

        return ChildHelper::getProviders($customer);
    }

    /**
     * Search panels
     * @return array
     */
    public function search()
    {
        $timezone = null;
        if (!Yii::$app->user->isGuest) {
            $timezone = Yii::$app->user->identity->timezone;
        }

        $providers = $this->getProviders();
        
        $errorsOrders = ArrayHelper::getColumn(Orders::find()->andWhere([
            'status' => Orders::STATUS_ERROR,
            'item' => Orders::ITEM_BUY_PANEL
        ])->all(), 'domain');

        $ordersStatuses = Orders::getStatuses();
        $projectsStatuses = Project::getStatuses();

        $prepareData = function ($value, $code) use ($providers, $ordersStatuses, $projectsStatuses, $timezone, $errorsOrders) {
            $access = [
                'isActive' => false,
                'isActivityLog' => false,
            ];

            $provider = $value['provider'];
            $value['provider'] = '';

            if (in_array($code, ['pending', 'canceled'])) {
                $options = (array)(@json_decode($provider, true));
                $provider = ArrayHelper::getValue($options, 'provider');
            }

            if (!empty($providers[$provider])) {
                $value['provider'] = $providers[$provider];
            }

            if ('pending' == $code) {
                $value['statusName'] = $ordersStatuses[Orders::STATUS_PENDING];
            } else if ('canceled' == $code) {
                $value['statusName'] = $ordersStatuses[Orders::STATUS_CANCELED];
            } else {
                // skip panels with errors
                if (in_array($value['domain'], $errorsOrders)) {
                    return null;
                }
                $value['statusName'] = $projectsStatuses[$value['status']];
                $value['expired'] = Yii::$app->formatter->asDate($value['expired'] + ((int)$timezone), 'php:Y-m-d H:i:s');

                $access['isActive'] = Project::hasAccess($value, 'canEdit');
                $access['isActivityLog'] = Project::hasAccess($value, 'canActivityLog');
            }

            $value['date'] = Yii::$app->formatter->asDate($value['date'] + ((int)$timezone), 'php:Y-m-d H:i:s');
            $value['domain'] = DomainsHelper::idnToUtf8($value['domain']);
            $value['access'] = $access;

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