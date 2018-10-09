<?php
namespace my\modules\superadmin\models\search;

use common\models\panels\AdditionalServices;
use common\models\panels\Project;
use my\helpers\Url;
use Yii;
use yii\data\Sort;
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
    public function getParams(): array
    {
        return [
            'query' => $this->getQuery(),
            'type' => isset($this->params['type']) ? $this->params['type'] : null,
            'script' => isset($this->params['script']) ? $this->params['script'] : null,
        ];
    }

    /**
     * Set value of page size
     * @return int|string
     */
    public function getPageSize()
    {
        $pageSize = isset($this->params['page_size']) ? $this->params['page_size'] : 100;
        return in_array($pageSize, static::$pageSizeList) ? $pageSize : 100;
    }

    /**
     * Build sql query
     * @param null|string $type
     * @param null|string $script
     * @return Query
     */
    public function buildQuery($type = null, $script = null): Query
    {
        $searchQuery = $this->getQuery();
        $script = $script == 'all' ? null : $script;


        $providers = (new Query())
            ->select([
                'id',
                'name',
                'provider_id',
                'start_count',
                'refill',
                'cancel',
                'service_view',
                'send_method',
                'type',
                'status',
                'date',
                'name_script',
                'apihelp',
                'sender_params',
                'service_options',
                'provider_service_id_label',
                'provider_service_settings',
                'provider_service_api_error',
                'service_description',
                'service_auto_min',
                'service_auto_max',
                'provider_rate',
                'service_auto_rate',
                'import',
                'getstatus_params',
                'service_count',
                'service_inuse_count',
            ])
            ->from('additional_services');

        if (!empty($searchQuery)) {
            $providers->andFilterWhere([
                'or',
                ['=', 'provider_id', $searchQuery],
                ['like', 'name', $searchQuery],
                ['like', 'name_script', $searchQuery],
                ['like', 'apihelp', $searchQuery],
            ]);
        }

        if (null !== $script) {
            $providers->andFilterWhere(['name_script' => $script]);
        }

        if (null !== $type) {
            $providers->andFilterWhere(['type' => $type]);
        }

        return $providers;
    }

    /**
     * @param null|string $type
     * @param null|string $script
     * @return Pagination
     */
    private function setPagination($type = null, $script = null): Pagination
    {
        $query = clone $this->buildQuery($type, $script);

        $pages = new Pagination(['totalCount' => $query->count()]);
        $pages->setPageSize($this->getPageSize());
        $pages->defaultPageSize = static::$pageSizeList[0];

        return $pages;
    }

    /**
     * Get providers
     * @param integer|null $type
     * @param $script|null string
     * @return Query
     */
    protected function getProviders($type = null, $script = null): Query
    {
        $query = clone $this->buildQuery($type, $script);
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
    public function search(): array
    {
        $type = ArrayHelper::getValue($this->params, 'type', null);
        $script = ArrayHelper::getValue($this->params, 'script', null);

        $sort = new Sort([
            'attributes' => [
                'provider_id' => [
                    'default' => SORT_DESC,
                    'label' => Yii::t('app/superadmin', 'providers.list.column_id'),
                ],
                'name' => [
                    'label' => Yii::t('app/superadmin', 'providers.list.column_name'),
                ],
                'send_method' => [
                    'label' => Yii::t('app/superadmin', 'providers.list.column_send_method'),
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
                'service_count' => [
                    'label' => Yii::t('app/superadmin', 'providers.list.column_count'),
                ],
                'service_inuse_count' => [
                    'label' => Yii::t('app/superadmin', 'providers.list.column_in_use'),
                ],
            ],
        ]);
        $sort->defaultOrder = [
            'provider_id' => SORT_DESC,
        ];

        $providers = $this->getProviders($type, $script)
            ->orderBy($sort->orders)
            ->all();

        return [
            'models' => $this->prepareRowData($providers),
            'pages' => $this->setPagination($type, $script),
            'sort' => $sort,
        ];
    }

    /**
     * Prepare provider data
     * @param mixed $providers
     * @return array
     */
    public function prepareRowData($providers): array
    {
        $returnProviders = [];

        $providersPanels = $this->getProviderPanels();

        foreach ($providers as $key => $provider) {
            $projects = ArrayHelper::getValue($providersPanels, $provider['provider_id'], []);
            $usedProjects = [];

            foreach ($projects as $id => $project) {
                if (!empty($project['providers'][$provider['provider_id']])) {
                    $usedProjects[] = $project;
                }
                $projects[$id]['url'] = $project['child_panel'] == 0 ? Url::toRoute(['/panels', 'id' => $project['id']]) : Url::toRoute(['/child-panels', 'id' => $project['id']]);
            }

            $returnProviders[$key] = [
                'form_data' => $provider,
                'id' => $provider['id'],
                'provider_id' => $provider['provider_id'],
                'name' => $provider['name'],
                'count' => $provider['service_count'],
                'projects' => array_values($projects),
                'in_use' => $provider['service_inuse_count'],
                'usedProjects' => array_values($usedProjects),
                'start_count' => AdditionalServices::getStartCountName($provider['start_count']),
                'refill' => AdditionalServices::getRefillName($provider['refill']),
                'cancel' => AdditionalServices::getCancelName($provider['cancel']),
                'type' => AdditionalServices::getTypeNameString($provider['type']),
                'status' => AdditionalServices::getStatusNameString($provider['status']),
                'date' => $provider['date'],
                'statusName' => $provider['status'],
                'service_view' => AdditionalServices::getServiceViewName($provider['service_view']),
                'send_method' => AdditionalServices::getAutoOrderName($provider['send_method']),
                'name_script' => $provider['name_script'],
                'apihelp' => $provider['apihelp'],
                'sender_params' => $provider['sender_params'],
                'service_options' => $provider['service_options'],
                'provider_service_id_label' => $provider['provider_service_id_label'],
                'provider_service_settings' => $provider['provider_service_settings'],
                'provider_service_api_error' => $provider['provider_service_api_error'],
                'service_description' => $provider['service_description'],
                'service_auto_min' => $provider['service_auto_min'],
                'service_auto_max' => $provider['service_auto_max'],
                'provider_rate' => $provider['provider_rate'],
                'service_auto_rate' => $provider['service_auto_rate'],
                'import' => $provider['import'],
                'getstatus_params' => $provider['getstatus_params'],
            ];
        }

        return $returnProviders;
    }

    /**
     * @return array
     */
    public function getScripts(): array
    {
        $type = ArrayHelper::getValue($this->params, 'type', null);
        $searchQuery = $this->getQuery();

        $scripts = (new Query())
            ->select([
                'name_script',
                'COUNT(id) as count'
            ])
            ->from(DB_PANELS . '.additional_services')
            ->groupBy('name_script');

        if (!empty($searchQuery)) {
            $scripts->andFilterWhere([
                'or',
                ['=', 'provider_id', $searchQuery],
                ['like', 'name', $searchQuery],
                ['like', 'name_script', $searchQuery],
            ]);
        }

        $returnArray = [];
        $allCount = 0;
        foreach ($scripts->all() as $script) {
            $returnArray[] = [
                'name_script' => $script['name_script'],
                'string' => Yii::t('app/superadmin', 'providers.list.script', [
                    'script' => $script['name_script'],
                    'count' => $script['count'],
                ])
            ];
            $allCount += $script['count'];
        }
        $returnArray = array_merge([count($returnArray) =>
            [
                'name_script' => 'all',
                'string' => Yii::t('app/superadmin', 'providers.list.script_all', [
                    'count' => $allCount,
                ])
            ]
        ], $returnArray);

        return $returnArray;
    }

    /**
     * Get all projects
     * @return array
     */
    public function getProjects(): array
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
                 'site',
                 'child_panel'
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
    public function getProviderPanels(): array
    {
        if (!empty($this->_providerPanels)) {
            return $this->_providerPanels;
        }

        $projects = $this->getProjects();

        foreach ((new Query())
                     ->select(['provider_id', 'panel_id'])
                     ->from('user_services')
                     ->batch(100) as $userServices) {

            foreach ($userServices as $userService) {
                if (empty($projects[$userService['panel_id']])) {
                    continue;
                }

                $this->_providerPanels[$userService['provider_id']][$userService['panel_id']] = $projects[$userService['panel_id']];
            }
        }

        return $this->_providerPanels;
    }

    /**
     * Get navs
     * @return array
     */
    public function navs(): array
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