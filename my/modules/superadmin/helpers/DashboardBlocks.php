<?php

namespace superadmin\helpers;

use superadmin\models\search\dashboard\BaseBlock;
use superadmin\models\search\dashboard\ChildPanels;
use superadmin\models\search\dashboard\Domains;
use superadmin\models\search\dashboard\Panels;
use superadmin\models\search\dashboard\SSL;
use superadmin\models\search\dashboard\Stores;
use ReflectionClass;
use ReflectionMethod;
use Yii;

/*
 * Helper class for receiving dashboard panels
 */
class DashboardBlocks
{
    const BLOCK_PANELS = 'panels';
    const BLOCK_CHILD_PANELS = 'child-panels';
    const BLOCK_STORES = 'stores';
    const BLOCK_DOMAINS = 'domains';
    const BLOCK_SSL = 'ssl';

    /**
     * Get block
     * @param $key
     * @return BaseBlock
     */
    public static function getBlock($key)
    {
        $config = static::_getConfig();
        if (empty($config[$key])) {
            return null;
        }

        $class = $config[$key]['source'];
        $reflectionClass = new ReflectionClass($class);

        /* @var BaseBlock $panel */
        $panel = $reflectionClass->getMethod('getInstance')->invoke(null);

        $panel->setName($config[$key]['name']);

        return $panel;
    }

    /**
     * Get panels config
     * @return array
     */
    private static function _getConfig()
    {
        return [
            self::BLOCK_PANELS => [
                'source' => Panels::class,
                'name' => Yii::t('app/superadmin', 'dashboard.panels')
            ],
            self::BLOCK_CHILD_PANELS => [
                'source' => ChildPanels::class,
                'name' => Yii::t('app/superadmin', 'dashboard.child_panels')
            ],
            self::BLOCK_STORES => [
                'source' => Stores::class,
                'name' => Yii::t('app/superadmin', 'dashboard.stores')
            ],
            self::BLOCK_DOMAINS => [
                'source' => Domains::class,
                'name' => Yii::t('app/superadmin', 'dashboard.domains')
            ],
            self::BLOCK_SSL => [
                'source' => SSL::class,
                'name' => Yii::t('app/superadmin', 'dashboard.ssl')
            ]
        ];
    }
    
    /**
     * Get panels
     * @return array
     */
    public static function getBlocks()
    {
        $panels = [];
        $config = static::_getConfig();
        foreach ($config as $key => $panel) {
            $panels[$key] = static::getBlock($key);
        }
        return $panels;
    }

    /**
     * Get column labels
     * @return array
     */
    public static function getLabels()
    {
       return [
           'id' => Yii::t('app/superadmin', 'dashboard.table.id'),
           'domain' => Yii::t('app/superadmin', 'dashboard.table.domain'),
           'created' => Yii::t('app/superadmin', 'dashboard.table.created'),
           'customer' => Yii::t('app/superadmin', 'dashboard.table.customer'),
           'expiry' => Yii::t('app/superadmin', 'dashboard.table.expiry'),
       ];
    }

}