<?php

namespace sommerce\modules\admin\models\search;

use common\models\stores\Integrations;
use common\models\stores\StoreIntegrations;
use common\models\stores\Stores;
use yii\base\Model;

/**
 * Class IntegrationsSearch
 * @package sommerce\modules\admin\models\search
 */
class IntegrationsSearch extends Model
{
    /** @var Stores */
    private $store;

    /**
     * Set store
     * @param Stores $store
     */
    public function setStore(Stores $store)
    {
        $this->store = $store;
    }

    public function search(): array
    {
        $integrations = StoreIntegrations::find()
            ->where(['store_id' => $this->store->id])
            ->orderBy(['position' => SORT_ASC])
            ->asArray()
            ->all();

        return $this->prepareData($integrations);
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
                'options' => $storeIntegration['options'],
                'position' => isset($storeIntegration['position']) ? $storeIntegration['position'] : $inegration['position'],
                'visibility' => $storeIntegration['visibility'],
                'name' => $inegration['name'],
            ];

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