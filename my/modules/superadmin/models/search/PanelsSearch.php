<?php
namespace my\modules\superadmin\models\search;

use common\helpers\CurrencyHelper;
use my\helpers\DomainsHelper;
use Yii;
use common\models\panels\Project;
use common\models\panels\Tariff;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class PanelsSearch
 * @package my\modules\superadmin\models\search
 */
class PanelsSearch {

    /**
     * @var array
     */
    protected $_projects = [];

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
     * @return array
     */
    public function _getPanels($status = null, $plan = null)
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
            'customers.email AS customer_email',
            'customers.referrer_id AS referrer_id',
            'COUNT(DISTINCT pr2.id) as panels'
        ]);
        $projects->leftJoin('project as pr2', 'pr2.cid = project.cid AND pr2.child_panel = project.child_panel');
        $projects->leftJoin('customers', 'customers.id = project.cid');

        return $projects->orderBy([
            'project.id' => SORT_DESC
        ])->groupBy('project.id')
            ->all();
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

        foreach ((new Query())
                     ->select('id, before_orders, of_orders, title')
                     ->from('tariff')
                     ->all() as $tariff) {
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

        foreach ((new Query())
                     ->select('pid, aid')
                     ->from('user_services')
                     ->all() as $provider) {
            $this->_userServices[$provider['pid']][] = $provider['aid'];
        }

        return $this->_userServices;
    }

    /**
     * Get panels
     * @param null|string|integer $status
     * @param null|integer $plan
     * @return PanelsSearch|array
     */
    public function getPanels($status = null, $plan = null)
    {
        if (empty($this->_projects)) {
            $this->_projects = $this->_getPanels();
        }

        if ((null === $status || 'all' === $status) && null === $plan) {
            return $this->_projects;
        }

        $projects = [];

        foreach ($this->_projects as $project) {
            if (is_numeric($status) && (int)$project['act'] != (int)$status) {
                continue;
            }

            if (is_numeric($plan) && (int)$project['plan'] != (int)$plan) {
                continue;
            }

            $projects[] = $project;
        }

        return $projects;
    }

    /**
     * Search panels
     * @return array
     */
    public function search()
    {
        $status = ArrayHelper::getValue($this->params, 'status', 'all');
        $plan = isset($this->params['plan']) ? (int)$this->params['plan'] : null;


        return [
            'models' => $this->preparePanelsData($this->getPanels($status, $plan))
        ];
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
        $skipSomeData = function($panels) {
            foreach ($panels as $key => $panel) {
                if (0 == $panel['plan']) {
                    unset($panels[$key]);
                    continue;
                }
            }

            return $panels;
        };

        return [
            'all' => 'All (' . count($skipSomeData($this->getPanels('all'))) . ')',
            Project::STATUS_ACTIVE => 'Active (' . count($skipSomeData($this->getPanels(Project::STATUS_ACTIVE))) . ')',
            Project::STATUS_FROZEN => 'Frozen (' . count($skipSomeData($this->getPanels(Project::STATUS_FROZEN))) . ')',
            Project::STATUS_TERMINATED => 'Terminated (' . count($skipSomeData($this->getPanels(Project::STATUS_TERMINATED))) . ')',
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
                'count' => count($this->getPanels($status))
            ])
        ];

        foreach ($plans as $plan) {
            // Не выводим тарифы -1
            if (0 > $plan['id']) {
                continue;
            }
            $returnPlans[$plan['id']] = $plan['title'] . ' (' . count($this->getPanels($status, $plan['id'])) . ')';
        }

        return $returnPlans;
    }
}