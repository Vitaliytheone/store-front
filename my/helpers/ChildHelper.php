<?php

namespace my\helpers;

use common\models\panels\AdditionalServices;
use common\models\panels\PanelLanguages;
use common\models\panels\Project;
use yii\db\Query;
use Yii;

/**
 * Class ChildHelper
 * @package my\helpers
 */
class ChildHelper
{
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
            $providers[$provider['provider_id']] = DomainsHelper::idnToUtf8($provider['name']);
        }

        return $providers;
    }

    /**
     * Set languages for child panel
     * @param Project $child
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function setChildLanguages(Project $child): bool
    {
        $providerModel = AdditionalServices::findOne(['provider_id' => $child->provider_id]);
        $parent = Project::findOne(['site' => $providerModel->name]);
        $languages = $parent->getChildPanelLanguages();

        if (!empty($languages)) {
            foreach ($languages as $lang) {
                $language = PanelLanguages::findOne($lang);
                if (!$language) {
                    continue;
                }

                $exist = (new Query())
                    ->select('code')
                    ->from($child->db . '.languages')
                    ->where(['code' => $lang])
                    ->exists();
                if ($exist) {
                    continue;
                }

                $position = (new Query())
                    ->select('MAX(position) + 1')
                    ->from($child->db . '.languages')
                    ->scalar();
                $position = isset($position) ? $position : 1;

                $rows = Yii::$app->db->createCommand()->insert($child->db . '.languages',
                        array_merge($language->attributes, [
                            'position' => $position,
                        ])
                    )->execute();
                if (!$rows) {
                    return false;
                }
            }
        }

        return true;
    }
}
