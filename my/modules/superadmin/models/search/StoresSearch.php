<?php
namespace superadmin\models\search;

use common\models\panels\Domains;
use common\models\stores\StoreDomains;
use common\models\stores\Stores;
use my\helpers\DomainsHelper;
use Yii;
use yii\data\Pagination;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use my\helpers\SpecialCharsHelper;

/**
 * Class StoresSearch
 * @package superadmin\models\search
 */
class StoresSearch {

    use SearchTrait;

    /** Store trial mode key name */
    const TRIAL_MODE_KEY = 'trial';

    public static $pageSizeList = [100, 500, 1000, 5000, 'all'];

    /**
     * @var array
     */
    protected $_stores = [];

    protected $_counts_by_status;

    /**
     * Get parameters
     * @return array
     */
    public function getParams(): array
    {
        return [
            'query' => $this->getQuery(),
            'status' => isset($this->params['status']) ? $this->params['status'] : 'all',
        ];
    }

    /**
     * Set value of page size
     * @return int|string
     */
    public function setPageSize()
    {
        $pageSize = isset($this->params['page_size']) ? $this->params['page_size'] : 100;
        return in_array($pageSize, static::$pageSizeList) ? $pageSize : 100;
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
                    'stores.status' => Stores::STATUS_ACTIVE,
                    'stores.trial' => Stores::TRIAL_MODE_ON,
                ]);
            }
        }

        $stores->leftJoin(DB_PANELS . '.customers', 'customers.id = stores.customer_id');

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

        return $stores;
    }

    /**
     * Search stores
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function search(): array
    {
        $status = isset($this->params['status']) ? $this->params['status'] : 'all';

        $query = clone $this->buildQuery($status);

        $pages = new Pagination(['totalCount' => $query->count()]);
        $pages->setPageSize($this->setPageSize());
        $pages->defaultPageSize = static::$pageSizeList[0];

        if (!empty($this->params['pageSize'])) {
            $pages->setPageSize($this->params['pageSize']);
        }

        $stores = $query
            ->select([
                'stores.id',
                'stores.domain',
                'stores.subdomain',
                'stores.currency',
                'stores.language',
                'stores.name',
                'stores.customer_id',
                'stores.status',
                'stores.created_at',
                'stores.expired',
                'stores.last_count',
                'stores.current_count',
                'customers.email AS customer_email',
                'customers.referrer_id AS referrer_id',
                'store_domains.domain AS store_domain',
            ])
            ->leftJoin(DB_STORES . '.store_domains', 'store_domains.store_id = stores.id AND store_domains.type IN (' . implode(",", [
                StoreDomains::DOMAIN_TYPE_DEFAULT,
                StoreDomains::DOMAIN_TYPE_SUBDOMAIN,
                StoreDomains::DOMAIN_TYPE_SOMMERCE
            ]). ')');

        $stores->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy([
                'stores.id' => SORT_DESC
            ])
            ->groupBy('stores.id');

        return [
            'models' => SpecialCharsHelper::multiPurifier($this->prepareStoresData($stores->all())),
            'pages' => $pages,
        ];
    }

    /**
     * Get prepared stores
     * @param array $stores
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    protected function prepareStoresData(array $stores): array
    {
        $returnStores = [];
        $domains = Domains::find()
            ->where('domain != ""')
            ->indexBy('domain')
            ->all();

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
                'last_count' => $store['last_count'],
                'current_count' => $store['current_count'],
                'name' => $store['name'],
                'isOurDomain' => array_key_exists($store['domain'], $domains),
            ];
        }

        return $returnStores;
    }

    /**
     * Get navs
     * @return array
     */
    public function navs(): array
    {
        return [
            'all' => Yii::t('app/superadmin', 'stores.list.navs_all', [
                'count' => $this->buildQuery()->count()
            ]),
            Stores::STATUS_ACTIVE => Yii::t('app/superadmin', 'stores.list.navs_active', [
                'count' => $this->buildQuery(Stores::STATUS_ACTIVE)->count()
            ]),
            self::TRIAL_MODE_KEY => Yii::t('app/superadmin', 'stores.list.navs_trial', [
                'count' => $this->buildQuery(self::TRIAL_MODE_KEY)->count()
            ]),
            Stores::STATUS_FROZEN => Yii::t('app/superadmin', 'stores.list.navs_frozen', [
                'count' => $this->buildQuery(Stores::STATUS_FROZEN)->count()
            ]),
            Stores::STATUS_TERMINATED => Yii::t('app/superadmin', 'stores.list.navs_terminated', [
                'count' => $this->buildQuery(Stores::STATUS_TERMINATED)->count()
            ]),
        ];
    }
}
