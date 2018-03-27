<?php

namespace sommerce\helpers;

use common\models\store\Packages;
use yii\helpers\Url;

/**
 * Class PackageHelper
 * @package sommerce\helpers
 */
class PackageHelper
{
    /**
     * Return package by now array
     * @param $packageId
     * @return array|bool
     */
    public static function getPackageBuyNow($packageId)
    {
        $package = Packages::findOne([
            'id' => $packageId,
            'visibility' => Packages::VISIBILITY_YES,
            'deleted' => Packages::DELETED_NO,
        ]);

        if(!$package) {
            return false;
        }

        return [
            'id' => $package->id,
            'title' => $package->name,
            'quantity' => $package->quantity,
            'price' => $package->price,
            'best' => $package->best,
            'url_buy_now' => Url::toRoute(['cart/order', 'id' =>$package->id], true),
        ];
    }
}