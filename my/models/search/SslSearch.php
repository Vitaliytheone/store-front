<?php

namespace my\models\search;

use my\helpers\DomainsHelper;
use common\models\panels\SslCertItem;
use Yii;
use common\models\panels\Orders;
use common\models\panels\SslCert;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class SslSearch
 * @package my\models\search
 */
class SslSearch
{
    private $params;

    public $rows;

    /**
     * Set search parameters
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * Build main search query
     * @return ActiveQuery the newly created [[ActiveQuery]] instance.
     */
    private function buildQuery()
    {
        $customer = ArrayHelper::getValue($this->params, 'customer_id');

        $orderPending = (new Query())
            ->select(['id', '("order") AS type', '(' . Orders::STATUS_PENDING . ') AS status', 'date', '(NULL) AS expired', 'details', 'domain AS domain', '(NULL) AS item_id'])
            ->from('orders')
            ->andWhere([
                'cid' => $customer,
                'status' => [
                    Orders::STATUS_PENDING,
                    Orders::STATUS_PAID,
                    Orders::STATUS_ERROR
                ],
                'item' => Orders::ITEM_BUY_SSL
            ])->orderBy([
                'id' => SORT_ASC
            ]);

        $orderCanceled = (new Query())
            ->select(['id', '("order") AS type', 'status', 'date', '(NULL) AS expired', 'details', 'domain AS domain', '(NULL) AS item_id'])
            ->from('orders')
            ->andWhere([
                'cid' => $customer,
                'status' => Orders::STATUS_CANCELED,
                'item' => Orders::ITEM_BUY_SSL
            ])->orderBy([
                'id' => SORT_ASC
            ]);

        $sslPending = (new Query())
            ->select(['sc.id', '("ssl") AS type', 'sc.status', 'sc.created_at AS date', '(NULL) AS expired', 'sc.details', 'sc.domain', 'sc.item_id'])
            ->from('ssl_cert sc')
            ->andWhere([
                'sc.cid' => $customer,
                'sc.status' => SslCert::STATUS_PENDING,
                'sc.checked' => SslCert::CHECKED_NO
            ]);

        $ssl = (new Query())
            ->select(['sc.id', '("ssl") AS type', 'sc.status', 'sc.created_at AS date', 'sc.expiry AS expired', 'sc.details', 'sc.domain', 'sc.item_id'])
            ->from('ssl_cert sc')
            ->andWhere([
                'sc.cid' => $customer,
                'sc.status' => [
                    SslCert::STATUS_ACTIVE,
                    SslCert::STATUS_ERROR,
                    SslCert::STATUS_PROCESSING,
                    SslCert::STATUS_PAYMENT_NEEDED,
                    SslCert::STATUS_INCOMPLETE,
                    SslCert::STATUS_EXPIRED,
                ]
            ])
            ->orderBy([
                new Expression('FIELD (sc.status, ' . implode(',', [
                        SslCert::STATUS_ACTIVE,
                        SslCert::STATUS_ERROR,
                        SslCert::STATUS_PROCESSING,
                        SslCert::STATUS_PAYMENT_NEEDED,
                        SslCert::STATUS_INCOMPLETE,
                        SslCert::STATUS_EXPIRED,
                    ]) . ')'),
                'sc.id' => SORT_ASC
            ]);

        return [
            $orderPending,
            $sslPending,
            $ssl,
            $orderCanceled
        ];
    }

    /**
     * Search tickets
     * @return array
     */
    public function search()
    {
        $timezone = null;
        if (!Yii::$app->user->isGuest) {
            $timezone = Yii::$app->user->identity->timezone;
        }

        $queries = $this->buildQuery();

        $sslItems = ArrayHelper::index(SslCertItem::find()->all(), 'id');


        $ordersStatuses = Orders::getStatuses();
        $sslStatuses = SslCert::getStatuses();

        $prepareData = function ($value) use ($sslItems, $ordersStatuses, $sslStatuses, $timezone) {
            if ('order' == $value['type']) {
                $value['statusName'] = $ordersStatuses[(int)$value['status']];
                $details = Json::decode($value['details']);
                $value['item_id'] = ArrayHelper::getValue($details, 'item_id');
                $value['sslItem'] = ArrayHelper::getValue($sslItems, $value['item_id'])->name;
            } else {
                $value['sslItem'] = ArrayHelper::getValue($sslItems, $value['item_id'])->name;
                $value['statusName'] = $sslStatuses[$value['status']];
            }

            $value['date'] = Yii::$app->formatter->asDate($value['date'] + ((int)$timezone), 'php:Y-m-d H:i:s');
            $value['domain'] = DomainsHelper::idnToUtf8($value['domain']);

            return $value;
        };

        $return = [];

        foreach ($queries as $query) {
            $data = $query->all();

            foreach ($data as $value) {
                $return[] = $prepareData($value);
            }
        }


        return $return;
    }
}