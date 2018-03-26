<?php
namespace my\modules\superadmin\models\search;

use common\helpers\CurrencyHelper;
use common\models\stores\Stores;
use my\helpers\DomainsHelper;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class StoresSearch
 * @package my\modules\superadmin\models\search
 */
class StoresSearch {

    /**
     * @var array
     */
    protected $_stores = [];

    protected $pageSize = 500;

    use SearchTrait;

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
     * Build sql query
     * @param int $status
     * @return array
     */
    public function _getStores($status = null)
    {
        $searchQuery = $this->getQuery();
        $customerId = ArrayHelper::getValue($this->params, 'customer_id');
        $id = ArrayHelper::getValue($this->params, 'id');

        $projects = (new Query())
            ->from(DB_STORES . '.stores');

        if (!('all' === $status || null === $status)) {
            $projects->andWhere([
                'stores.status' => $status
            ]);
        }

        if (!empty($searchQuery)) {
            $projects->andFilterWhere([
                'or',
                ['=', 'stores.id', $searchQuery],
                ['like', 'stores.name', $searchQuery],
                ['like', 'customers.email', $searchQuery],
            ]);
        }

        if ($id) {
            $projects->andWhere([
                'stores.id' => $id
            ]);
        }

        if ($customerId) {
            $projects->andWhere([
                'stores.customer_id' => $customerId
            ]);
        }

        $projects->select([
            'stores.id',
            'stores.domain',
            'stores.currency',
            'stores.language',
            'stores.customer_id',
            'stores.status',
            'stores.created_at',
            'stores.expired',
            'customers.email AS customer_email'
        ]);
        $projects->leftJoin(DB_STORES . '.customers', 'customers.id = stores.customer_id');

        return $projects->orderBy([
            'stores.id' => SORT_DESC
        ])->groupBy('stores.id')
            ->all();
    }

    /**
     * Get panels
     * @param null|string|integer $status
     * @param null|integer $plan
     * @return PanelsSearch|array
     */
    public function getStores($status = null)
    {
        if (empty($this->_stores)) {
            $this->_stores = $this->_getStores();
        }

        if ((null === $status || 'all' === $status)) {
            return $this->_stores;
        }

        $stores = [];

        foreach ($this->_stores as $store) {
            if (is_numeric($status) && (int)$store['status'] != (int)$status) {
                continue;
            }

            $stores[] = $store;
        }

        return $stores;
    }

    /**
     * Search panels
     * @return array
     */
    public function search()
    {
        $status = ArrayHelper::getValue($this->params, 'status', 'all');

        return [
            'models' => $this->preparePanelsData($this->getStores($status))
        ];
    }

    /**
     * Get prepared stores
     * @param array $stores
     * @return array
     */
    protected function preparePanelsData($stores)
    {
        $returnStores = [];

        foreach ($stores as $store) {
            $returnStores[] = [
                'id' => $store['id'],
                'domain' => DomainsHelper::idnToUtf8($store['domain']),
                'currency' => CurrencyHelper::getCurrencyCodeById($store['currency']),
                'lang' => strtoupper((string)$store['language']),
                'customer_id' => $store['customer_id'],
                'status' => Stores::getActNameString($store['status']),
                'created' => Stores::formatDate($store['created_at']),
                'expired' => Stores::formatDate($store['expired']),
                'created_date' => Stores::formatDate($store['created_at'], 'php:Y-m-d'),
                'created_time' => Stores::formatDate($store['created_at'], 'php:H:i:s'),
                'expired_date' => Stores::formatDate($store['expired'], 'php:Y-m-d'),
                'expired_time' => Stores::formatDate($store['expired'], 'php:H:i:s'),
                'customer_email' => $store['customer_email'],
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
        return [
            'all' => Yii::t('app/superadmin', 'stores.list.navs_all', [
                'count' => count($this->getStores('all'))
            ]),
            Stores::STATUS_ACTIVE => Yii::t('app/superadmin', 'stores.list.navs_active', [
                'count' => count($this->getStores(Stores::STATUS_ACTIVE))
            ]),
            Stores::STATUS_FROZEN => Yii::t('app/superadmin', 'stores.list.navs_frozen', [
                'count' => count($this->getStores(Stores::STATUS_FROZEN))
            ]),
            Stores::STATUS_TERMINATED => Yii::t('app/superadmin', 'stores.list.navs_terminated', [
                'count' => count($this->getStores(Stores::STATUS_TERMINATED))
            ]),
        ];
    }
}