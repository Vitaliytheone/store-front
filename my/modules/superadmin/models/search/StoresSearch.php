<?php
namespace my\modules\superadmin\models\search;

use common\models\stores\Stores;
use my\helpers\DomainsHelper;
use Yii;
use common\models\panels\Project;
use common\models\panels\Tariff;
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
            ->from(DB_PANELS . '.stores');

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
                ['like', 'stores.domain', $searchQuery],
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
        ]);
        $projects->leftJoin(DB_PANELS . '.customers', 'customers.id = project.cid');

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
        $returnPanels = [];
        'stores.id',
            'stores.domain',
            'stores.currency',
            'stores.language',
            'stores.customer_id',
            'stores.status',
            'stores.created_at',
            'stores.expired',
        foreach ($stores as $store) {
            $returnPanels[] = [
                'id' => $store['id'],
                'domain' => DomainsHelper::idnToUtf8($store['domain']),
                'currency' => Project::getCurrencyCodeById($store['currency']),
                'lang' => strtoupper((string)$store['language']),
                'customer_id' => $store['customer_id'],
                'status' => Stores::getActNameString($panel['act']),
                'created_date' => Project::formatDate($panel['date'], 'php:Y-m-d'),
                'created_time' => Project::formatDate($panel['date'], 'php:H:i:s'),
                'expired_date' => Project::formatDate($panel['expired'], 'php:Y-m-d'),
                'expired_time' => Project::formatDate($panel['expired'], 'php:H:i:s'),
                'expired_datetime' => Project::formatDate($panel['expired']),
                'expired' => $panel['expired'],
                'subdomain' => $panel['subdomain'],
                'date' => $panel['date'],
                'customer_email' => $panel['customer_email'],
                'referrer_id' => $panel['referrer_id'],
                'providers' => ArrayHelper::getValue($providers, $panel['id'], []),
                'can' => [
                    'downgrade' => 1 < $panel['panels']
                ]
            ];
        }

        return $returnPanels;
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