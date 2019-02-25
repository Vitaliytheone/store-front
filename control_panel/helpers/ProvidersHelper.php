<?php
namespace control_panel\helpers;
use common\models\panels\AdditionalServices;

/**
 * Class ProvidersHelper
 * @package control_panel\helpers
 */
class ProvidersHelper
{
    /**
     * Make providers old and remove possible duplicates by $providerName
     * @param string $providerName
     */
    public static function makeProvidersOld(string $providerName)
    {
        $providers = AdditionalServices::find()
            ->andWhere([
                'name' => $providerName
            ])
            ->all();

        foreach ($providers as $provider) {
            $provider->name = $provider->name . '_' . $provider->provider_id . '_old';
            $provider->status = AdditionalServices::STATUS_BROKEN;
            $provider->update(false);
        }
    }
}