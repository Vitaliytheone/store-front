<?php

namespace sommerce\modules\admin\models\search;

use common\models\sommerces\Integrations;
use common\models\sommerces\StoreIntegrations;
use common\models\sommerces\Stores;
use yii\base\Model;
use yii\db\ActiveQuery;

/**
 * Class IntegrationsSearch
 * @package sommerce\modules\admin\models\search
 */
class IntegrationsSearch extends Model
{
    /** @var Stores */
    private $store;

    /** @var null|int */
    private $integrationId = null;

    /**
     * Set store
     * @param Stores $store
     */
    public function setStore(Stores $store)
    {
        $this->store = $store;
    }

    /**
     * Set integration id
     * @param int $id
     */
    public function setIntegrationId(int $id)
    {
        $this->integrationId = $id;
    }

    /**
     * @return array|ActiveQuery
     */
    public function search(): ?array
    {
        $integrations = StoreIntegrations::find()
            ->where(['store_id' => $this->store->id]);

        if ($this->integrationId) {
            $integrations->andWhere(['id' => $this->integrationId]);
        }

        $integrations->orderBy(['position' => SORT_ASC])
            ->asArray();

        if (!$integrations->all()) {
            return null;
        }

        return $this->prepareData($integrations->all());
    }

    /**
     * Prepare data
     * @param array $data
     * @return array
     */
    private function prepareData(array $data): array
    {
        $integrations = Integrations::find()->asArray()->indexBy('id')->all();
        $analytics = [];
        $chats = [];

        foreach ($data as $key => $storeIntegration) {
            if (!isset($integrations[$storeIntegration['integration_id']])) {
                continue;
            }

            $inegration = $integrations[$storeIntegration['integration_id']];
            $integrationCategory = $inegration['category'];

            $item = [
                'id' => $storeIntegration['id'],
                'category' => $integrationCategory,
                'code' => $inegration['code'],
                'options' => isset($storeIntegration['options']) ? json_decode($storeIntegration['options'], true) : [],
                'position' => isset($storeIntegration['position']) ? $storeIntegration['position'] : $inegration['position'],
                'visibility' => $storeIntegration['visibility'],
                'name' => $inegration['name'],
                'settings_description' => $inegration['settings_description'],
                'settings_form' => isset($inegration['settings_form']) ? json_decode($inegration['settings_form'], true) : [],
            ];

            if ($this->integrationId) {
                return $item;
            }

            switch ($integrationCategory) {
                case Integrations::CATEGORY_CHATS:
                    $chats[] = $item;
                    break;
                case Integrations::CATEGORY_ANALYTICS:
                    $analytics[] = $item;
                    break;
            }
        }

        return [
            'chats' => $chats,
            'analytics' => $analytics,
        ];
    }
}