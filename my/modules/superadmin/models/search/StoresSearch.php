<?php
namespace my\modules\superadmin\models\search;

use common\helpers\CurrencyHelper;
use common\models\stores\StoreDomains;
use common\models\stores\Stores;
use my\helpers\DomainsHelper;
use Yii;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class StoresSearch
 * @package my\modules\superadmin\models\search
 */
class StoresSearch {

    use SearchTrait;

    /** Store trial mode key name */
    const TRIAL_MODE_KEY = 'trial';

    protected $pageSize = 100;

    /**
     * @var array
     */
    protected $_stores = [];

    protected $_counts_by_status;

    /**
     * Get parameters
     * @return array
     */
    public function getParams()
    {
        return [
            'query' => $this->getQuery(),
            'status' => isset($this->params['status']) ? $this->params['status'] : 'all',
        ];
    }

    /**
     * Build main search query
     * @param int $status
     * @return Query the newly created [[ActiveQuery]] instance.
     */
    private function buildQuery($status = null)
    {
        $searchQuery = $this->getQuery();
        $customerId = ArrayHelper::getValue($this->params, 'customer_id');
        $id = ArrayHelper::getValue($this->params, 'id');

        $stores = (new Query())
            ->from(DB_STORES . '.stores');

        if (!('all' === $status || null === $status)) {

            // For store statuses
            if (in_array($status, array_keys(Stores::getStatuses()))) {

                if ($status == Stores::STATUS_ACTIVE) {
                    $stores->andWhere([
                        'stores.trial' => Stores::TRIAL_MODE_OFF,
                    ]);
                }

                $stores->andWhere([
                    'stores.status' => $status
                ]);
            }

            // For trial mode
            if ($status == self::TRIAL_MODE_KEY) {
                $stores->andWhere([
                    'stores.trial' => Stores::TRIAL_MODE_ON,
                ]);
            }
        }

        if (!empty($searchQuery)) {
            $stores->andFilterWhere([
                'or',
                ['=', 'stores.id', $searchQuery],
                ['like', 'stores.domain', $searchQuery],
                ['like', 'customers.email', $searchQuery],
            ]);
        }

        if ($id) {
            $stores->andWhere([
                'stores.id' => $id
            ]);
        }

        if ($customerId) {
            $stores->andWhere([
                'stores.customer_id' => $customerId
            ]);
        }

        $stores->select([
            'stores.id',
            'stores.domain',
            'stores.subdomain',
            'stores.currency',
            'stores.language',
            'stores.customer_id',
            'stores.status',
            'stores.created_at',
            'stores.expired',
            'customers.email AS customer_email',
            'customers.referrer_id AS referrer_id',
            'store_domains.domain AS store_domain',
        ]);
        $stores->leftJoin(DB_PANELS . '.customers', 'customers.id = stores.customer_id');
        $stores->leftJoin(DB_STORES . '.store_domains', 'store_domains.store_id = stores.id AND store_domains.type IN (' . implode(",", [
            StoreDomains::DOMAIN_TYPE_DEFAULT,
            StoreDomains::DOMAIN_TYPE_SUBDOMAIN
        ]). ')');

        return $stores;
    }

    /**
     * Search stores
     * @return array
     */
    public function search()
    {
        $this->setCountsByStatus();

        $status = isset($this->params['status']) ? $this->params['status'] : 'all';

        $query = clone $this->buildQuery($status);

        $pages = new Pagination(['totalCount' => $this->getCountByStatus($status)]);
        $pages->setPageSize($this->pageSize);
        $pages->defaultPageSize = $this->pageSize;

        if (!empty($this->params['pageSize'])) {
            $pages->setPageSize($this->params['pageSize']);
        }

        $stores = $query
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy([
                'stores.id' => SORT_DESC
            ])
            ->groupBy('stores.id')
            ->all();

        return [
            'models' => $this->prepareStoresData($stores),
            'pages' => $pages,
        ];
    }

    /**
     * Get prepared stores
     * @param array $stores
     * @return array
     */
    protected function prepareStoresData($stores)
    {
        $returnStores = [];

        foreach ($stores as $store) {
            $returnStores[] = [
                'id' => $store['id'],
                'domain' => DomainsHelper::idnToUtf8($store['domain']),
                'subdomain' => $store['subdomain'],
                'currency' => $store['currency'],
                'language' => strtoupper((string)$store['language']),
                'customer_id' => $store['customer_id'],
                'status' => $store['status'],
                'status_name' => Stores::getActNameString($store['status']),
                'created' => Stores::formatDate($store['created_at']),
                'expired' => $store['expired'],
                'expired_datetime' => Stores::formatDate($store['expired']),
                'created_date' => Stores::formatDate($store['created_at'], 'php:Y-m-d'),
                'created_time' => Stores::formatDate($store['created_at'], 'php:H:i:s'),
                'expired_date' => !empty($store['expired']) ? Stores::formatDate($store['expired'], 'php:Y-m-d') : null,
                'expired_time' => !empty($store['expired']) ? Stores::formatDate($store['expired'], 'php:H:i:s') : null,
                'customer_email' => $store['customer_email'],
                'referrer_id' => $store['referrer_id'],
                'store_domain' => $store['store_domain'],
            ];
        }

        return $returnStores;
    }

    /**
     * Get navs
     * @return array
     */
    public function navs()
    {
        $countsByStatus = $this->getCountsByStatuses();

        return [
            'all' => Yii::t('app/superadmin', 'stores.list.navs_all', [
                'count' => array_sum($countsByStatus)
            ]),
            Stores::STATUS_ACTIVE => Yii::t('app/superadmin', 'stores.list.navs_active', [
                'count' => ArrayHelper::getValue($countsByStatus, Stores::STATUS_ACTIVE, 0)
            ]),
            self::TRIAL_MODE_KEY => Yii::t('app/superadmin', 'stores.list.navs_trial', [
                'count' => ArrayHelper::getValue($countsByStatus, self::TRIAL_MODE_KEY, 0)
            ]),
            Stores::STATUS_FROZEN => Yii::t('app/superadmin', 'stores.list.navs_frozen', [
                'count' => ArrayHelper::getValue($countsByStatus, Stores::STATUS_FROZEN, 0)
            ]),
            Stores::STATUS_TERMINATED => Yii::t('app/superadmin', 'stores.list.navs_terminated', [
                'count' => ArrayHelper::getValue($countsByStatus, Stores::STATUS_TERMINATED, 0)
            ]),
        ];
    }

    /**
     * Return topics count for status or all
     * @param null $status
     * @return float|int|mixed
     */
    public function getCountByStatus($status = null)
    {
        if ($status === null || $status === 'all') {
            return array_sum($this->_counts_by_status);
        }

        return ArrayHelper::getValue($this->_counts_by_status, $status);
    }

    /**
     * Return cached counted tickets for statuses
     * considering search query string
     * @return array
     */
    public function setCountsByStatus()
    {
        $query = clone $this->buildQuery(null);

        $stores = $query
            ->select(['id' => 'stores.id', 'status' => 'stores.status', 'trial' => 'stores.trial'])
            ->groupBy('id')
            ->all();

        $this->_counts_by_status = [
            Stores::STATUS_ACTIVE => count(array_filter($stores, function($store){
                return
                    $store['status'] == Stores::STATUS_ACTIVE &&
                    $store['trial'] == Stores::TRIAL_MODE_OFF;
            })),
            Stores::STATUS_FROZEN => count(array_filter($stores, function($store){
                return $store['status'] == Stores::STATUS_FROZEN;
            })),
            Stores::STATUS_TERMINATED => count(array_filter($stores, function($store){
                return $store['status'] == Stores::STATUS_TERMINATED;
            })),
            self::TRIAL_MODE_KEY => count(array_filter($stores, function($store){
                return $store['trial'] == Stores::TRIAL_MODE_ON;
            })),
        ];

        return $this->_counts_by_status;
    }

    /**
     * Return topics counts for each status
     * @return array
     */
    public function getCountsByStatuses()
    {
        return $this->_counts_by_status;
    }
}