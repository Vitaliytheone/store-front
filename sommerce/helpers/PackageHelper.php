<?php

namespace sommerce\helpers;

use common\models\sommerce\Packages;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Class PackageHelper
 * @package sommerce\helpers
 */
class PackageHelper
{
    /**
     * @var array
     */
    public static $packages;

    public static function getPackages()
    {
        if (null !== static::$packages) {
            return static::$packages;
        }

        static::$packages = [];

        foreach (Packages::find()
            ->select([
                'id',
                'name',
                'quantity',
                'price',
                'best',
                'product_id',
                'icon',
                'properties',
            ])
            ->andWhere([
                'visibility' => Packages::VISIBILITY_YES,
                'deleted' => Packages::DELETED_NO,
            ])
            ->asArray()
            ->all() as $package) {
            static::$packages[$package['id']] = $package;
        }

        return static::$packages;
    }

    /**
     * Return package by now array
     * @param $packageId
     * @return array|bool
     */
    public static function getPackageById($packageId)
    {
        $package = ArrayHelper::getValue(static::getPackages(), $packageId);

        if (!$package) {
            return null;
        }

        return [
            'id' => $package['id'],
            'title' => $package['name'],
            'quantity' => $package['quantity'],
            'price' => $package['price'],
            'best' => $package['best'],
            'url_buy_now' => Url::toRoute(['cart/order', 'id' => $package['id']], true),
        ];
    }

    /**
     * @param integer $productId
     * @return array
     */
    public static function getPackagesByProductId($productId)
    {
        $packages = (array)ArrayHelper::index(static::getPackages(), null, 'product_id');
        $packages = (array)ArrayHelper::getValue($packages, $productId, []);

        return ArrayHelper::getColumn($packages, function ($package) {
            return [
                'id' => $package['id'],
                'name' => $package['name'],
                'price' => $package['price'],
                'quantity' => $package['quantity'],
                'icon' => $package['icon'],
                'properties' => $package['properties'],
            ];
        });
    }
}