<?php

namespace console\components;

use yii\db\Query;
use common\models\panels\Project;
use common\models\panels\AdditionalServices;
use yii\helpers\ArrayHelper;

class UpdateServicesCount
{

    /**
     * Update counts
     */
    public function run()
    {
        $providers = $this->getProviders();

        $providersPanels = $this->getProviderPanels();

        foreach ($providers as $key => $provider) {
            $projects = ArrayHelper::getValue($providersPanels, $provider['panel_id'], []);
            $usedProjects = [];

            foreach ($projects as $project) {
                if (!empty($project['providers'][$provider['panel_id']])) {
                    $usedProjects[] = $project;
                }
            }

            AdditionalServices::updateAll(
                ['service_count' => count(array_values($projects)), 'service_inuse_count' => count(array_values($usedProjects))],
                ['panel_id' => $provider['panel_id']]
            );
        }
    }

    /**
     * @return array
     */
    public function getProviders()
    {
        $providers = (new Query())
            ->select([
                'panel_id',
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
        $providerPanels = [];
        if (!empty($providerPanels)) {
            return $providerPanels;
        }

        $projects = $this->getProjects();

        foreach ((new Query())
                     ->select(['provider_id as aid', 'panel_id as pid'])
                     ->from('user_services')
                     ->batch(100) as $userServices) {

            foreach ($userServices as $userService) {
                if (empty($projects[$userService['pid']])) {
                    continue;
                }

                $providerPanels[$userService['aid']][$userService['pid']] = $projects[$userService['pid']];
            }
        }

        return $providerPanels;
    }

    /**
     * Get all projects
     * @return array
     */
    public function getProjects()
    {
        $projects = [];
        if (!empty($projects)) {
            return $projects;
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

            $projects[$project['id']] = array_merge($project, [
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

            $projects[$project['id']]['providers'] = $providers;
        }

        return $projects;
    }
}