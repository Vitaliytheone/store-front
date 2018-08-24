<?php

namespace console\helpers;

use yii\db\Query;
use common\models\panels\Project;

class UpdateServicesCountHelper
{

    /**
     * @return array
     */
    public static function buildQuery()
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
    public static function getProviderPanels()
    {
        $providerPanels = [];
        if (!empty($providerPanels)) {
            return $providerPanels;
        }

        $projects = static::getProjects();

        foreach ((new Query())
                     ->select(['aid', 'pid'])
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
    public static function getProjects()
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