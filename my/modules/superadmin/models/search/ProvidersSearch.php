<?php
namespace my\modules\superadmin\models\search;

use common\models\panels\AdditionalServices;
use common\models\panels\Project;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class ProvidersSearch
 * @package my\modules\superadmin\models\search
 */
class ProvidersSearch {

    use SearchTrait;

    protected $_providers = [];
    protected $_projects = [];
    protected $_providerPanels = [];

    /**
     * Get parameters
     * @return array
     */
    public function getParams()
    {
        return [
            'query' => $this->getQuery(),
            'type' => isset($this->params['type']) ? $this->params['type'] : null
        ];
    }

    /**
     * Build sql query
     * @return ActiveRecord
     */
    public function buildQuery()
    {
        $searchQuery = $this->getQuery();

        $providers = (new Query())
            ->select([
                'id',
                'name',
                'res',
                'sc',
                'refill',
                'cancel',
                'auto_services',
                'auto_order',
                'type',
                'status',
                'date',
                'skype',
            ])
            ->from('additional_services');

        if (!empty($searchQuery)) {
            $providers->andFilterWhere([
                'or',
                ['=', 'res', $searchQuery],
                ['like', 'name', $searchQuery],
            ]);
        }

        return $providers;
    }

    /**
     * Get providers
     * @param integer $type
     * @return array
     */
    protected function getProviders($type = null)
    {
        if (empty($this->_providers)) {
            $query = clone $this->buildQuery();

            $this->_providers = $query
                ->orderBy([
                    'id' => SORT_DESC
                ])
                ->all();
        }

        if (null !== $type) {
            $providers = [];
            foreach ($this->_providers as $provider) {
                if ($provider['type'] == $type) {
                    $providers[] = $provider;
                }
            }

            return $providers;
        }

        return $this->_providers;
    }

    /**
     * Search providers
     * @return array
     */
    public function search()
    {
        $type = ArrayHelper::getValue($this->params, 'type', null);

        $providers = $this->getProviders($type);

        return [
            'models' => $this->prepareRowData($providers),
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
                'sc' => $provider['sc'],
                'refill' => $provider['refill'],
                'cancel' => $provider['cancel'],
                'auto_services' => $provider['auto_services'],
                'auto_order' => $provider['auto_order'],
                'type' => AdditionalServices::getTypeNameString($provider['type']),
                'status' => $provider['status'],
                'date' => $provider['date'],
                'skype' => $provider['skype'],
                'statusName' => AdditionalServices::getStatusNameString($provider['status']),
            ];
        }

        return $returnProviders;
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
                    'res'
                ])
                ->from($project['db'] . '.services')
                ->andWhere([
                    'act' => 1
                ])
                ->all() as $service) {
                $providers[$service['res']] = $service['res'];
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
                'count' => count($this->getProviders())
            ]),
            AdditionalServices::TYPE_INTERNAL => Yii::t('app/superadmin', 'providers.list.navs_internal', [
                'count' => count($this->getProviders(AdditionalServices::TYPE_INTERNAL))
            ]),
            AdditionalServices::TYPE_EXTERNAL => Yii::t('app/superadmin', 'providers.list.navs_external', [
                'count' => count($this->getProviders(AdditionalServices::TYPE_EXTERNAL))
            ])
        ];
    }
}