<?php
namespace my\modules\superadmin\models\search;

use common\helpers\CurrencyHelper;
use my\helpers\DomainsHelper;
use Yii;
use common\models\panels\Project;
use common\models\panels\Tariff;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\db\Connection;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class PanelsSearch
 * @package my\modules\superadmin\models\search
 */
class PanelsSearch {

    protected $pageSize = 100;

    /**
     * @var array
     */
    protected $_tariffs = [];

    /**
     * @var array
     */
    protected $_userServices = [];

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
            'plan' => isset($this->params['plan']) ? (int)$this->params['plan'] : null
        ];
    }

    /**
     * Build sql query
     * @param int $status
     * @param int $plan
     * @param array $filters
     * @return Query
     */
    public function buildQuery($status = null, $plan = null, $filters = [])
    {
        $searchQuery = $this->getQuery();
        $customerId = ArrayHelper::getValue($this->params, 'customer_id');
        $id = ArrayHelper::getValue($this->params, 'id');
        $child = ArrayHelper::getValue($this->params, 'child');

        $projects = (new Query())
            ->from('project');

        if ('all' === $status || null === $status) {
            if (empty($searchQuery)) {
                $projects->andWhere([
                    'project.act' => [
                        Project::STATUS_ACTIVE,
                        Project::STATUS_FROZEN,
                        Project::STATUS_TERMINATED,
                    ]
                ]);
            }
        } else {
            $projects->andWhere([
                'project.act' => $status
            ]);
        }

        if (null !== $plan) {
            $projects->andWhere([
                'project.plan' => $plan
            ]);
        }

        if ($child) {
            $projects->andWhere([
                'project.child_panel' => $child
            ]);
        } else {
            $projects->andWhere([
                'project.child_panel' => 0
            ]);
        }

        if (!empty($searchQuery)) {
            $projects->andFilterWhere([
                'or',
                ['=', 'project.id', $searchQuery],
                ['like', 'project.name', $searchQuery],
                ['like', 'project.site', $searchQuery],
            ]);
        }

        if (isset($filters['skip']['plan'])) {
            $projects->andWhere('project.plan <> ' . (int)$filters['skip']['plan']);
        }

        if ($id) {
            $projects->andWhere([
                'project.id' => $id
            ]);
        }

        if ($customerId) {
            $projects->andWhere([
                'project.cid' => $customerId
            ]);
        }

        $projects->select([
            'project.id',
            'project.site',
            'project.currency',
            'project.lang',
            'project.cid',
            'project.plan',
            'project.last_count',
            'project.current_count',
            'project.forecast_count',
            'project.act',
            'project.expired',
            'project.subdomain',
            'project.date',
            'project.no_invoice',
            'customers.email AS customer_email',
            'customers.referrer_id AS referrer_id',
            'COUNT(DISTINCT pr2.id) as panels'
        ]);
        $projects->leftJoin('project as pr2', 'pr2.cid = project.cid AND pr2.child_panel = project.child_panel');
        $projects->leftJoin('customers', 'customers.id = project.cid');

        return $projects;
    }

    /**
     * Get all tariffs
     * @return array
     */
    protected function getTariffs()
    {
        if (!empty($this->_tariffs)) {
            return $this->_tariffs;
        }

        $query = (new Query())
            ->select('id, before_orders, of_orders, title')
            ->from('tariff');

        foreach (static::queryAllCache($query) as $tariff) {
            $this->_tariffs[$tariff['id']] = $tariff;
        }

        return $this->_tariffs;
    }

    /**
     * Get all providers
     * @return array
     */
    protected function getProviders()
    {
        if (!empty($this->_userServices)) {
            return $this->_userServices;
        }

        $query = (new Query())
            ->select('pid, aid')
            ->from('user_services');

        foreach (static::queryAllCache($query) as $provider) {
            $this->_userServices[$provider['pid']][] = $provider['aid'];
        }

        return $this->_userServices;
    }

    /**
     * Search panels
     * @return array
     */
    public function search()
    {
        $status = ArrayHelper::getValue($this->params, 'status', 'all');
        $plan = isset($this->params['plan']) ? (int)$this->params['plan'] : null;

        $query = clone $this->buildQuery($status, $plan);

        $pages = new Pagination(['totalCount' => $this->count($status, $plan)]);
        $pages->setPageSize($this->pageSize);
        $pages->defaultPageSize = $this->pageSize;

        if (!empty($this->params['pageSize'])) {
            $pages->setPageSize($this->params['pageSize']);
        }

        $panels = $query
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->groupBy('project.id')
            ->orderBy([
                'project.id' => SORT_DESC
            ]);

        return [
            'models' => $this->preparePanelsData(static::queryAllCache($panels)),
            'pages' => $pages,
        ];
    }

    /**
     * Get count panels by type
     * @param int $status
     * @param int $plan
     * @param array $filters
     * @return int|array
     */
    public function count($status = null, $plan = null, $filters = [])
    {
        $query = clone $this->buildQuery($status, $plan, $filters);

        if (!empty($filters['group']['status'])) {
            $query->select([
                'project.act as status',
                'COUNT(DISTINCT project.id) as rows'
            ])->groupBy('project.act');

            return ArrayHelper::map(static::queryAllCache($query), 'status', 'rows');
        }

        if (!empty($filters['group']['plan'])) {
            $query->select([
                'project.plan as plan',
                'COUNT(DISTINCT project.id) as rows'
            ])->groupBy('project.plan');

            return ArrayHelper::map(static::queryAllCache($query), 'plan', 'rows');
        }

        $query = $query->select('COUNT(DISTINCT project.id)');

        return (int)static::queryScalarCache($query);
    }

    /**
     * Get prefared panels
     * @param array $panels
     * @return array
     */
    protected function preparePanelsData($panels)
    {
        $returnPanels = [];

        $tariffs = $this->getTariffs();
        $providers = $this->getProviders();

        foreach ($panels as $panel) {
            $tariff = ArrayHelper::getValue($tariffs, $panel['plan']);
            $returnPanels[] = [
                'id' => $panel['id'],
                'site' => DomainsHelper::idnToUtf8($panel['site']),
                'currency' => CurrencyHelper::getCurrencyCodeById($panel['currency']),
                'lang' => strtoupper($panel['lang']),
                'cid' => $panel['cid'],
                'tariff' => ArrayHelper::getValue($tariff, 'title'),
                'before_orders' => ArrayHelper::getValue($tariff, 'before_orders'),
                'of_orders' => ArrayHelper::getValue($tariff, 'of_orders'),
                'last_count' => $panel['last_count'],
                'current_count' => $panel['current_count'],
                'forecast_count' => $panel['forecast_count'],
                'act' => $panel['act'],
                'status' => Project::getActNameString($panel['act']),
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
                'no_invoice' => $panel['no_invoice'],
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
        $statusCounters = $this->count(null, null, [
            'group' => [
                'status' => 1
            ],
            'skip' => [
                'plan' => 0
            ]
        ]);

        return [
            'all' => 'All (' . $this->count('all', null, [
                'skip' => [
                    'plan' => 0
                ]
            ]) . ')',
            Project::STATUS_ACTIVE => 'Active (' . ArrayHelper::getValue($statusCounters, Project::STATUS_ACTIVE, 0) . ')',
            Project::STATUS_FROZEN => 'Frozen (' . ArrayHelper::getValue($statusCounters, Project::STATUS_FROZEN, 0) . ')',
            Project::STATUS_TERMINATED => 'Terminated (' . ArrayHelper::getValue($statusCounters, Project::STATUS_TERMINATED, 0) . ')',
        ];
    }

    /**
     * Get aggregated plans
     * @return array
     */
    public function getAggregatedPlans()
    {
        $status = isset($this->params['status']) ? $this->params['status'] : 'all';

        $plans = $this->getTariffs();

        $options = [];
        if (Project::STATUS_ACTIVE == $status) {
            $options = [
                'skip' => [
                    'plan' => 0
                ]
            ];
        }

        $returnPlans = [
            null => Yii::t('app/superadmin', 'panels.list.navs_method_all', [
                'count' => $this->count($status, null, $options)
            ])
        ];



        $plansCounters = $this->count($status, null, [
            'group' => [
                'plan' => 1
            ],
        ]);

        foreach ($plans as $plan) {
            // Не выводим тарифы -1
            if (0 > (int)$plan['id']) {
                continue;
            }
            $returnPlans[$plan['id']] = $plan['title'] . ' (' . ArrayHelper::getValue($plansCounters, $plan['id'], 0) . ')';
        }

        return $returnPlans;
    }
}