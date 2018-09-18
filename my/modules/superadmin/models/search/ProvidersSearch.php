<?php
namespace my\modules\superadmin\models\search;

use common\models\panels\AdditionalServices;
use common\models\panels\Project;
use Yii;
use yii\data\Sort;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\data\Pagination;

/**
 * Class ProvidersSearch
 * @package my\modules\superadmin\models\search
 */
class ProvidersSearch
{

    protected $_providers = [];
    protected $_projects = [];
    protected $_providerPanels = [];

    private static $pageSizeList = [100, 500, 1000, 5000, 'all'];

    use SearchTrait;

    /**
     * Get parameters
     * @return array
     */
    public function getParams()
    {
        return [
            'query' => $this->getQuery(),
            'type' => isset($this->params['type']) ? $this->params['type'] : null,
            'plan' => isset($this->params['plan']) ? $this->params['plan'] : null,
        ];
    }

    /**
     * Set value of page size
     */
    public function getPageSize()
    {
        $pageSize = isset($this->params['page_size']) ? $this->params['page_size'] : 100;
        return in_array($pageSize, static::$pageSizeList) ? $pageSize : 100;
    }

    /**
     * Build sql query
     * @param $type
     * @param $plan
     * @return Query
     */
    public function buildQuery($type = null, $plan = null)
    {
        $searchQuery = $this->getQuery();
        $plan = $plan == 'all' ? null : $plan;


        $providers = (new Query())
            ->select([
                'id',
                'name',
                'res',
                'start_count',
                'refill',
                'cancel',
                'service_view',
                'auto_order',
                'type',
                'status',
                'date',
                'name_script',
            ])
            ->from('additional_services');

        if (!empty($searchQuery)) {
            $providers->andFilterWhere([
                'or',
                ['=', 'res', $searchQuery],
                ['like', 'name', $searchQuery],
                ['like', 'name_script', $searchQuery],
            ]);
        }

        if (null !== $plan) {
            $providers->andFilterWhere(['name_script' => $plan]);
        }

        if (null !== $type) {
            $providers->andFilterWhere(['type' => $type]);
        }

        return $providers;
    }

    /**
     * @param null|string $type
     * @return Pagination
     */
    private function setPagination($type = null, $plan = null)
    {
        $query = clone $this->buildQuery($type, $plan);

        $pages = new Pagination(['totalCount' => $query->count()]);
        $pages->setPageSize($this->getPageSize());
        $pages->defaultPageSize = static::$pageSizeList[0];

        return $pages;
    }

    /**
     * Get providers
     * @param integer $type
     * @param $plan string
     * @return Query
     */
    protected function getProviders($type = null, $plan = null)
    {
        $query = clone $this->buildQuery($type, $plan);
        $pages = $this->setPagination($type);

        $this->_providers = $query
            ->offset($pages->offset)
            ->limit($pages->limit);

        return $this->_providers;
    }

    /**
     * Search providers
     * @return array
     */
    public function search()
    {
        $type = ArrayHelper::getValue($this->params, 'type', null);
        $plan = ArrayHelper::getValue($this->params, 'plan', null);

        $sort = new Sort([
            'attributes' => [
                'res' => [
                    'default' => SORT_DESC,
                    'label' => Yii::t('app/superadmin', 'providers.list.column_id'),
                ],
                'name' => [
                    'label' => Yii::t('app/superadmin', 'providers.list.column_name'),
                ],
                'auto_order' => [
                    'label' => Yii::t('app/superadmin', 'providers.list.column_sender'),
                ],
                'type' => [
                    'label' => Yii::t('app/superadmin', 'providers.list.column_type'),
                ],
                'status' => [
                    'label' => Yii::t('app/superadmin', 'providers.list.column_status'),
                ],
                'start_count' => [
                    'label' => Yii::t('app/superadmin', 'providers.list.column_start_count'),
                ],
                'refill' => [
                    'label' => Yii::t('app/superadmin', 'providers.list.column_refill'),
                ],
                'cancel' => [
                    'label' => Yii::t('app/superadmin', 'providers.list.column_cancel'),
                ],
                'service_view' => [
                    'label' => Yii::t('app/superadmin', 'providers.list.column_autolist'),
                ],
                'date' => [
                    'label' => Yii::t('app/superadmin', 'providers.list.column_created'),
                ],
            ],
        ]);
        $sort->defaultOrder = [
            'res' => SORT_DESC,
        ];

        $providers = $this->buildQuery($type, $plan)
            ->orderBy($sort->orders)
            ->all();

        return [
            'models' => $this->prepareRowData($providers),
            'pages' => $this->setPagination($type, $plan),
            'sort' => $sort,
        ];
    }

    /**
     * Prepare provider data
     * @param mixed $providers
     * @return array
     */
    public function prepareRowData($providers)
    {
        $returnProviders = [];

        $providersPanels = $this->getProviderPanels();

        foreach ($providers as $key => $provider) {
            $projects = ArrayHelper::getValue($providersPanels, $provider['res'], []);
            $usedProjects = [];

            foreach ($projects as $project) {
                if (!empty($project['providers'][$provider['res']])) {
                    $usedProjects[] = $project;
                }
            }

            $returnProviders[$key] = [
                'id' => $provider['id'],
                'res' => $provider['res'],
                'name' => $provider['name'],
                'projects' => array_values($projects),
                'usedProjects' => array_values($usedProjects),
                'start_count' => AdditionalServices::getStartCountName($provider['start_count']),
                'refill' => AdditionalServices::getRefillName($provider['refill']),
                'cancel' => AdditionalServices::getCancelName($provider['cancel']),
                'type' => AdditionalServices::getTypeNameString($provider['type']),
                'status' => $provider['status'],
                'date' => $provider['date'],
                'statusName' => AdditionalServices::getStatusNameString($provider['status']),
                'service_view' => AdditionalServices::getServiceViewName($provider['service_view']),
                'auto_order' => AdditionalServices::getAutoOrderName($provider['auto_order']),
                'name_script' => $provider['name_script'],
            ];
        }

        return $returnProviders;
    }

    /**
     * @return array
     */
    public function getPlans(): array
    {
        $type = ArrayHelper::getValue($this->params, 'type', null);
        $searchQuery = $this->getQuery();

        $plans = (new Query())
            ->select([
                'name_script',
                'COUNT(id) as count'
            ])
            ->from(DB_PANELS . '.additional_services')
            ->groupBy('name_script');

        if (!empty($searchQuery)) {
            $plans->andFilterWhere([
                'or',
                ['=', 'res', $searchQuery],
                ['like', 'name', $searchQuery],
                ['like', 'name_script', $searchQuery],
            ]);
        }

        $allCount = $this->buildQuery($type)->count();
        $returnArray = array_merge($plans->all(), ['all' =>
            [
                'label' => Yii::t('app/superadmin', 'providers.list.plan_all'),
                'count' => $allCount,
            ]
        ]);

        return $returnArray;
    }

    /**
     * Get all projects
     * @return array
     */
    public function getProjects()
    {
        if (!empty($this->_projects)) {
            return $this->_projects;
        }

        foreach ((new Query())
             ->select([
                 'id',
                 'act',
                 'db',
                 'name',
                 'site'
             ])
             ->from('project')
             ->andWhere([
                 'act' => Project::STATUS_ACTIVE
             ])
            ->andWhere("db <>''")
             ->all() as $project) {

            $this->_projects[$project['id']] = array_merge($project, [
                'providers' => []
            ]);

            $providers = [];

            foreach ((new Query())
                ->select([
                    'provider_id'
                ])
                ->from($project['db'] . '.services')
                ->andWhere([
                    'act' => 1
                ])
                ->all() as $service) {
                $providers[$service['provider_id']] = $service['provider_id'];
            }

            $this->_projects[$project['id']]['providers'] = $providers;
        }

        return $this->_projects;
    }

    /**
     * Get all user services data
     * @return array
     */
    public function getProviderPanels()
    {
        if (!empty($this->_providerPanels)) {
            return $this->_providerPanels;
        }

        $projects = $this->getProjects();

        foreach ((new Query())
            ->select(['aid', 'pid'])
            ->from('user_services')
            ->batch(100) as $userServices) {

            foreach ($userServices as $userService) {
                if (empty($projects[$userService['pid']])) {
                    continue;
                }

                $this->_providerPanels[$userService['aid']][$userService['pid']] = $projects[$userService['pid']];
            }
        }

        return $this->_providerPanels;
    }

    /**
     * Get navs
     * @return array
     */
    public function navs()
    {
        return [
            null => Yii::t('app/superadmin', 'providers.list.navs_all', [
                'count' => $this->buildQuery()->count()
            ]),
            AdditionalServices::TYPE_INTERNAL => Yii::t('app/superadmin', 'providers.list.navs_internal', [
                'count' => $this->buildQuery(AdditionalServices::TYPE_INTERNAL)->count()
            ]),
            AdditionalServices::TYPE_EXTERNAL => Yii::t('app/superadmin', 'providers.list.navs_external', [
                'count' => $this->buildQuery(AdditionalServices::TYPE_EXTERNAL)->count()
            ])
        ];
    }
}