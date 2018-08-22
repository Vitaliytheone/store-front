<?php

namespace console\controllers\superadmin;


use console\controllers\my\CustomController;
use yii\db\Query;
use common\models\panels\Project;
use yii\helpers\ArrayHelper;
use common\models\panels\AdditionalServices;

/**
 * Class UpdateServicesCount
 * @package console\controllers\superadmin
 */
class UpdateServicesCountController extends CustomController
{
    protected $_providers = [];
    protected $_projects = [];
    protected $_providerPanels = [];

    /**
     * Update service_count & service_inuse_count in additional_services
     */
    public function actionUpdate()
    {
        $providers = $this->buildQuery();

        $providersPanels = $this->getProviderPanels();

        foreach ($providers as $key => $provider) {
            $projects = ArrayHelper::getValue($providersPanels, $provider['res'], []);
            $usedProjects = [];

            foreach ($projects as $project) {
                if (!empty($project['providers'][$provider['res']])) {
                    $usedProjects[] = $project;
                }
            }

            $service = AdditionalServices::find()
                ->where(['res' => $provider['res']])
                ->one();
            $service->service_count = count(array_values($projects));
            $service->service_inuse_count = count(array_values($usedProjects));
            $service->update();
        }
    }

    /**
     * @return array
     */
    public function buildQuery()
    {
        $providers = (new Query())
            ->select([
                'res',
            ])
            ->from('additional_services')
            ->all();

        return $providers;
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
}