<?php

namespace superadmin\models\search;


use common\components\traits\UnixTimeFormatTrait;
use common\models\gateways\Sites;
use yii\base\Model;
use yii\data\Pagination;
use yii\db\Query;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class GatewaysSearch
 * @package superadmin\models\search
 */
class GatewaysSearch extends Model
{
    const DEFAULT_PAGE_SIZE = 100;

    protected static $pageSizes = [
        100,
        500,
        1000,
        5000,
        'all',
    ];

    use SearchTrait;
    use UnixTimeFormatTrait;

    /**
     * Get parameters
     * @return array
     */
    public function getParams()
    {
        return [
            'query' => $this->getQuery(),
            'status' => isset($this->params['status']) ? $this->params['status'] : 'all',
            'page_size' => isset($this->params['page_size']) ? $this->params['page_size'] : self::DEFAULT_PAGE_SIZE,
            'plan' => isset($this->params['plan']) ? (int)$this->params['plan'] : null
        ];
    }

    /**
     * Get page size
     * @return int|string
     */
    public function getPageSize()
    {
        $pageSize = ArrayHelper::getValue($this->getParams(), 'page_size');

        return ArrayHelper::getValue(static::$pageSizes, $pageSize, static::DEFAULT_PAGE_SIZE);
    }

    public function buildQuery($status = null)
    {
        $searchQuery = $this->getQuery();

        $gateways = (new Query())
            ->select([
                'sites.*',
                'customers.email AS customer_email',
            ])
            ->from(Sites::tableName())
            ->leftJoin('customers', 'customers.id = sites.customer_id');

        if ($status && $status != 'all') {
            $gateways->andWhere([
                'sites.status' => $status
            ]);
        }

        if (!empty($searchQuery)) {
            $gateways->andFilterWhere([
                'or',
                ['=', 'sites.id', $searchQuery],
                ['like', 'sites.domain', $searchQuery],
                ['like', 'customers.email', $searchQuery],
            ]);
        }

        return $gateways;
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function search(): array
    {
        $status = ArrayHelper::getValue($this->params, 'status', 'all');
        $query = clone $this->buildQuery($status);
        $pageSize = $this->getPageSize();

        if ('all' === $pageSize) {
            $pageSize = $query->count();
        }

        $pages = new Pagination(['totalCount' => $query->count()]);
        $pages->setPageSize($pageSize);
        $pages->defaultPageSize = static::DEFAULT_PAGE_SIZE;

        $model = $query
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy(['id' => SORT_DESC])
            ->all();

        return [
            'models' => $this->prepareData($model),
            'pages' => $pages,
        ];
    }

    /**
     * Prepare data
     * @param array $data
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function prepareData(array $data): array
    {
        $result = [];

        foreach ($data as $key => $gateway) {
            $result[$key] = [
                'id' => $gateway['id'],
                'domain' => $gateway['domain'],
                'subdomain' => $gateway['subdomain'],
                'customer_id' => $gateway['customer_id'],
                'customer_email' => $gateway['customer_email'],
                'status' => $gateway['status'],
                'status_name' => Sites::getStatusName($gateway['status']),
                'created' => $this->formatDate($gateway['created_at']),
                'expiry' => $this->formatDate($gateway['expired_at']),
            ];
        }

        return $result;
    }

    /**
     * Get navs
     * @return array
     */
    public function navs(): array
    {
        return [
            'all' => Yii::t('app/superadmin', 'gateways.list.nav.all', [
                'count' => $this->buildQuery()->count(),
            ]),
            Sites::STATUS_ACTIVE => Yii::t('app/superadmin', 'gateways.list.nav.active', [
                'count' => $this->buildQuery(Sites::STATUS_ACTIVE)->count(),
            ]),
            Sites::STATUS_FROZEN => Yii::t('app/superadmin', 'gateways.list.nav.frozen', [
                'count' => $this->buildQuery(Sites::STATUS_FROZEN)->count(),
            ]),
            Sites::STATUS_TERMINATED => Yii::t('app/superadmin', 'gateways.list.nav.terminated', [
                'count' => $this->buildQuery(Sites::STATUS_TERMINATED)->count(),
            ]),
        ];
    }
}