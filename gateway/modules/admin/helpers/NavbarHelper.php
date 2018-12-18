<?php
namespace admin\helpers;

use common\models\gateways\Admins;
use admin\components\Url;
use Yii;

/**
 * Class NavbarHelper
 * @package admin\helpers
 */
class NavbarHelper {

    /**
     * Return items list
     * @return array
     */
    private static function _NavbarItems(){
        return [
            'settings' => [
                'url' => '/admin/settings',
                'label' => 'Settings',
                'class' => 'mobile-hidden',
                'submenuItems' => [
                    'settings-general' => [
                        'url' => '/admin/settings',
                        'icon' => 'icon-settings',
                        'label' => Yii::t('admin', 'header.menu_settings_general'),
                    ],
                    'settings-payments' => [
                        'url' => '/admin/settings/payments',
                        'icon' => 'icon-wallet',
                        'label' => Yii::t('admin', 'header.menu_settings_payments'),
                    ],
                    'settings-navigation' => [
                        'url' => '/admin/settings/navigation',
                        'icon' => 'flaticon-list-1',
                        'label' => Yii::t('admin', 'header.menu_settings_navigation'),
                    ],
                    'settings-pages' => [
                        'url' => '/admin/settings/pages',
                        'icon' => 'icon-docs',
                        'label' => Yii::t('admin', 'header.menu_settings_pages'),
                    ],
                    'settings-themes' => [
                        'url' => '/admin/settings/themes',
                        'icon' => 'icon-puzzle',
                        'label' => Yii::t('admin', 'header.menu_settings_themes'),
                    ],
                ],
            ],
        ];
    }

    /**
     * Return is active item route
     * @param $itemRoute
     * @param $currentRoute
     * @return bool
     */
    private static function isItemActive($itemRoute, $currentRoute)
    {
        return stripos($currentRoute, ltrim($itemRoute,'/')) !== false;
    }

    /**
     * Return formatted top navigation menu items
     * @param $currentRoute
     * @return array
     */
    public static function getNavbarItems($currentRoute)
    {
        $navbarItems = static::_NavbarItems();

        /** @var Admins $user */
        $authUser = Yii::$app->user;
        $user = $authUser->getIdentity();

        /**
         * Populate $navbarItems by url and active class is menu item is active
         */
        array_walk($navbarItems, function(&$item, $itemKey) use ($currentRoute){
            $isItemActive = static::isItemActive($item['url'], $currentRoute);

            $item['active'] = $isItemActive;

            if (isset($item['url'])) {
                $item['url'] = Url::to($item['url']);
            }

            // Submenu items walk if exist
            if (isset($item['submenuItems']) && is_array($item['submenuItems'])) {
                array_walk($item['submenuItems'], function(&$subItem, $subItemKey) use ($item, $isItemActive){

                    $subItem['active'] = $isItemActive;

                    if (isset($subItem['url'])) {
                        $subItem['url'] = Url::to($subItem['url']);
                    }
                });
            }
        });

        return $navbarItems;
    }

}