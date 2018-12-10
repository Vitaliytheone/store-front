<?php
namespace superadmin\models\search;

use common\helpers\CurrencyHelper;
use common\models\panels\Customers;
use my\helpers\DomainsHelper;
use my\helpers\SpecialCharsHelper;
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
 * @package superadmin\models\search
 */
class PanelsSearch {
    const PAGE_SIZE_100 = 100;
    const PAGE_SIZE_500 = 500;
    const PAGE_SIZE_1000 = 1000;
    const PAGE_SIZE_5000 = 5000;
    const PAGE_SIZE_ALL = 'All';

    protected $pageSize = self::PAGE_SIZE_100;

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
     * @return array
     */
    public static function getPageSizes() {
        return [
            self::PAGE_SIZE_100,
            self::PAGE_SIZE_500,
            self::PAGE_SIZE_1000,
            self::PAGE_SIZE_5000,
            self::PAGE_SIZE_ALL
        ];
    }

    /**
     * Get parameters
     * @return array
     */
    public function getParams()
    {
        return [
            'query' => $this->getQuery(),
            'status' => isset($this->params['status']) ? $this->params['status'] : 'all',
            'page_size' => isset($this->params['page_size']) ? $this->params['page_size'] : self::PAGE_SIZE_100,
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

        $projects->leftJoin('customers as cust1', 'cust1.id = project.cid');

        if (!empty($searchQuery)) {
            $projects->andFilterWhere([
                'or',
                ['=', 'project.id', $searchQuery],
                ['like', 'project.name', $searchQuery],
                ['like', 'project.site', $searchQuery],
                ['like', 'cust1.email', $searchQuery],
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
            ->select('panel_id, provider_id')
            ->from('user_services');

        foreach (static::queryAllCache($query) as $provider) {
            $this->_userServices[$provider['panel_id']][] = $provider['provider_id'];
        }

        return $this->_userServices;
    }

    /**
     * Search panels
     * @return array
     */
    public function search()
    {
        $searchQuery = $this->getQuery();
        $status = ArrayHelper::getValue($this->params, 'status', 'all');
        $plan = isset($this->params['plan']) ? (int)$this->params['plan'] : null;

        $query = clone $this->buildQuery($status, $plan);

        $pages = new Pagination(['totalCount' => $this->count($status, $plan)]);
        $pages->setPageSize($this->pageSize);
        $pages->defaultPageSize = $this->pageSize;

        if (!empty($this->params['page_size'])
            && array_search($this->params['page_size'], static::getPageSizes()) !== false) {
            $pages->setPageSize($this->params['page_size']);
        }

        $query->select([
        'project.id',
        'project.site',
        'project.currency_code',
        'project.lang',
        'project.cid',
        'project.plan',
        'project.tariff',
        'project.last_count',
        'project.current_count',
        'project.forecast_count',
        'project.act',
        'project.expired',
        'project.subdomain',
        'project.date',
        'project.no_invoice',
        'cust1.email AS customer_email',
        'cust1.referrer_id AS referrer_id',
        'cust2.email as referrer',
        'COUNT(DISTINCT pr2.id) as panels',
        'project.name',
        'project.skype',
        'project.skype',
        'project.auto_order',
        'project.theme',
        'project.currency',
        'project.utc',
        'project.package',
        'project.seo',
        'project.comments',
        'project.mentions_wo_hashtag',
        'project.mentions',
        'project.mentions_custom',
        'project.mentions_hashtag',
        'project.mentions_follower',
        'project.mentions_likes',
        'project.writing',
        'project.drip_feed',
        'project.captcha',
        'project.name_modal',
        'project.custom',
        'project.start_count',
        'project.apikey',
        'project.affiliate_system',
    ]);
        $query->leftJoin('project as pr2', 'pr2.cid = project.cid AND pr2.child_panel = project.child_panel');
        $query->leftJoin('customers as cust2', 'cust2.id = cust1.referrer_id');

        if (empty($this->params['page_size']) || !$this->params['page_size'] != self::PAGE_SIZE_ALL) {
            $query = $query->offset($pages->offset)
                ->limit($pages->limit);
        }

        $panels = $query->groupBy('project.id')
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
            $futureTariff = ArrayHelper::getValue($tariffs, $panel['tariff']);
            $returnPanels[] = [
                'id' => $panel['id'],
                'plan' =>  $panel['plan'],
                'tariffId' => $panel['tariff'],
                'site' => DomainsHelper::idnToUtf8($panel['site']),
                'currency' => $panel['currency'],
                'lang' => strtoupper($panel['lang']),
                'cid' => $panel['cid'],
                'tariff' => ArrayHelper::getValue($tariff, 'title'),
                'futureTariff' => ArrayHelper::getValue($futureTariff, 'title'),
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
                'referrer_email' => $panel['referrer'],
                'providers' => ArrayHelper::getValue($providers, $panel['id'], []),
                'no_invoice' => $panel['no_invoice'],
                'can' => [
                    'downgrade' => 1 < $panel['panels']
                ],
                'name' => $panel['name'],
                'skype' => $panel['skype'],
                'auto_order' => $panel['auto_order'],
                'theme' => $panel['theme'],
                'currency_code' => $panel['currency_code'],
                'utc' => $panel['utc'],
                'package' => $panel['package'],
                'seo' => $panel['seo'],
                'comments' => $panel['comments'],
                'mentions_wo_hashtag' => $panel['mentions_wo_hashtag'],
                'mentions' => $panel['mentions'],
                'mentions_custom' => $panel['mentions_custom'],
                'mentions_hashtag' => $panel['mentions_hashtag'],
                'mentions_follower' => $panel['mentions_follower'],
                'mentions_likes' => $panel['mentions_likes'],
                'writing' => $panel['writing'],
                'drip_feed' => $panel['drip_feed'],
                'captcha' => $panel['captcha'],
                'name_modal' => $panel['name_modal'],
                'custom' => $panel['custom'],
                'start_count' => $panel['start_count'],
                'apikey' => $panel['apikey'],
                'affiliate_system' => $panel['affiliate_system'],
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
            'all' => Yii::t('app/superadmin', 'panels.list.nav_all', [
                'count' => $this->count('all', null, [
                    'skip' => [
                        'plan' => 0
                    ]
                ])
            ]),
            Project::STATUS_ACTIVE => Yii::t('app/superadmin', 'panels.list.nav_active', [
                'count' => ArrayHelper::getValue($statusCounters, Project::STATUS_ACTIVE, 0),
            ]),
            Project::STATUS_FROZEN => Yii::t('app/superadmin', 'panels.list.nav_frozen', [
                'count' => ArrayHelper::getValue($statusCounters, Project::STATUS_FROZEN, 0),
            ]),
            Project::STATUS_TERMINATED => Yii::t('app/superadmin', 'panels.list.nav_terminated', [
                'count' => ArrayHelper::getValue($statusCounters, Project::STATUS_TERMINATED, 0)
            ]),
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

        $returnPlans = [
            null => Yii::t('app/superadmin', 'panels.list.navs_method_all', [
                'count' => $this->count($status, null)
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