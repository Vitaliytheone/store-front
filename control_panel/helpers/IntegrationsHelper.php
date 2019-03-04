<?php

namespace control_panel\helpers;

use common\models\sommerces\Integrations;
use common\models\sommerces\StoreIntegrations;

/**
 * Class IntegrationsHelper
 * @package common\helpers
 */
class IntegrationsHelper
{
    /**
     * @param int $storeId
     * @return bool
     */
    public static function addStoreIntegrations(int $storeId): bool
    {
        $integrations = Integrations::find()->all();

        foreach ($integrations as $integration) {
            /** @var Integrations $integration */
            $storeIntegration = new StoreIntegrations();
            $storeIntegration->integration_id = $integration->id;
            $storeIntegration->store_id = $storeId;
            $storeIntegration->visibility = StoreIntegrations::VISIBILITY_OFF;
            $storeIntegration->position = $integration->position;

            if (!$storeIntegration->save(false)) {
                return false;
            }
        }

        return true;
    }
}
