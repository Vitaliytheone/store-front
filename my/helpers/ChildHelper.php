<?php
namespace my\helpers;

use yii\db\Query;

/**
 * Class ChildHelper
 * @package my\helpers
 */
class ChildHelper {

    /**
     * Get customer providers
     * @param $userId
     * @param array $statuses
     * @return array
     */
    public static function getProviders($userId, $statuses = [])
    {
        $providersQuery = (new Query())
            ->select(['additional_services.provider_id', 'additional_services.name'])
            ->from('additional_services')
            ->innerJoin('project', 'additional_services.name = project.site')
            ->andWhere([
                'project.cid' => $userId,
                'project.child_panel' => 0,
            ]);

        if (!empty($statuses)) {
            $providersQuery->andWhere([
                'project.act' => $statuses
            ]);
        }

        $providers = [];
        foreach ($providersQuery->all() as $provider) {
            $providers[$provider['res']] = DomainsHelper::idnToUtf8($provider['name']);
        }

        return $providers;
    }
}