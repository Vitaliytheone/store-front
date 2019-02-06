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
            'settings-payments' =>  [
                'url' => '/admin/settings/payments',
                'label' => Yii::t('admin', 'header.menu_settings_payments'),
            ],
            'settings-files' =>  [
                'url' => '/admin/settings/files',
                'label' => Yii::t('admin', 'header.menu_settings_files'),
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