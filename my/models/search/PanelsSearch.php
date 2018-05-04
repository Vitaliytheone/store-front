<?php

namespace my\models\search;

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
 * Class PanelsSearch
 * @package my\models\search
 */
class PanelsSearch
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
            ->select(['id', '("order") AS type', 'domain AS domain', 'status', 'date', '(NULL) AS plan', '(NULL) AS expired', '(NULL) AS db', 'hide'])
            ->from('orders')
            ->andWhere([
                'cid' => $customer,
                'status' => [
                    Orders::STATUS_PAID,
                    Orders::STATUS_PENDING,
                    Orders::STATUS_ERROR
                ],
                'item' => Orders::ITEM_BUY_PANEL,
            ])->orderBy([
                'id' => SORT_ASC
            ]);

        $orderCanceled = (new Query())
            ->select(['id', '("order") AS type', 'domain AS domain', 'status', 'date', '(NULL) AS plan', '(NULL) AS expired', '(NULL) AS db', 'hide'])
            ->from('orders')
            ->andWhere([
                'cid' => $customer,
                'status' => Orders::STATUS_CANCELED,
                'item' => Orders::ITEM_BUY_PANEL
            ])->orderBy([
                'id' => SORT_ASC
            ]);

        $projects = (new Query())
            ->select(['id', '("project") AS type', 'site as domain', 'act AS status', 'date', 'plan', 'expired', 'db', 'hide'])
            ->from('project')
            ->andWhere([
                'child_panel' => 0,
                'cid' => $customer,
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
     * Search panels
     * @return array
     */
    public function search()
    {
        $timezone = null;
        if (!Yii::$app->user->isGuest) {
            $timezone = Yii::$app->user->identity->timezone;
        }

        $tariffs = ArrayHelper::index(Tariff::find()->all(), 'id');
        $errorsOrders = ArrayHelper::getColumn(Orders::find()->andWhere([
            'status' => Orders::STATUS_ERROR,
            'item' => Orders::ITEM_BUY_PANEL
        ])->all(), 'domain');
        $defaultTariff = Tariff::findOne([
            'title' => 'Plan A'
        ]);
        $ordersStatuses = Orders::getStatuses();
        $projectsStatuses = Project::getStatuses();

        $prepareData = function ($value, $code) use ($tariffs, $defaultTariff, $ordersStatuses, $projectsStatuses, $timezone, $errorsOrders) {
            $access = [
                'isActive' => false,
                'isActivityLog' => false,
            ];
            if ('pending' == $code) {
                $value['statusName'] = $ordersStatuses[Orders::STATUS_PENDING];
                $value['plan'] = $defaultTariff->getFullName();
            } else if ('canceled' == $code) {
                $value['statusName'] = $ordersStatuses[Orders::STATUS_CANCELED];
                $value['plan'] = $defaultTariff->getFullName();
            } else {
                // skip panels with errors
                if (in_array($value['domain'], $errorsOrders)) {
                    return null;
                }
                $value['statusName'] = $projectsStatuses[$value['status']];
                $value['plan'] = ArrayHelper::getValue($tariffs, $value['plan'], $defaultTariff)->getFullName();
                $value['expired'] = Yii::$app->formatter->asDate($value['expired'] + ((int)$timezone), 'php:Y-m-d H:i:s');

                $access['isActive'] = Project::hasAccess($value, 'canEdit');
                $access['isActivityLog'] = Project::hasAccess($value, 'canActivityLog');
            }

            $value['date'] = Yii::$app->formatter->asDate($value['date'] + ((int)$timezone), 'php:Y-m-d H:i:s');
            $value['domain'] = DomainsHelper::idnToUtf8($value['domain']);
            $value['access'] = $access;
            $value['hide'] = ArrayHelper::getValue($value, 'hide', null);

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