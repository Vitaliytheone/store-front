<?php

namespace frontend\helpers;

use Yii;
use frontend\modules\admin\components\Url;

class NavigationHelper
{
    /**
     * Return Navigation menu item
     * @param $treeItem array
     * @return string
     */
    public static function menuItemTpl($treeItem)
    {
        $id = $treeItem['id'];
        $name = $treeItem['name'];
        $parentId = $treeItem['parent_id'];
        $link = $treeItem['link'];
        $linkId = $treeItem['link_id'];
        $position = $treeItem['position'];
        $url = $treeItem['url'];

        $updateUrl = Url::toRoute(['/settings/update-nav', 'id'=> $id]);
        $getNavUrl = Url::toRoute(['/settings/get-nav', 'id'=> $id]);
        $deleteUrl = Url::toRoute(['/settings/delete-nav', 'id'=> $id]);

        $editBtnText = Yii::t('admin', 'settings.nav_bt_edit');
        $deleteBtnText = Yii::t('admin', 'settings.nav_bt_delete');

        $menuItem = <<<EOT
            <div class="dd-handle">$name</div>
            <div class="dd-edit-button">
                <a href="#" class="btn m-btn--pill m-btn--air btn-primary btn-sm" data-submit_url="$updateUrl" data-get_url="$getNavUrl" data-toggle="modal" data-target=".edit_navigation">
                    $editBtnText
                </a>
                <a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-delete_url="$deleteUrl" data-toggle="modal" data-target="#delete-modal" data-backdrop="static" title="$deleteBtnText">
                    <i class="la la-trash"></i>
                </a>
            </div>
EOT;
        if (isset($treeItem['nodes'])) {

            $menuItem .= '<ol class="dd-list">' . static::menuTree($treeItem['nodes']) . '</ol>';
        }

        return '<li class="dd-item" data-id="'. $id .'">' . $menuItem . '</li>';
    }


    /**
     * Return navigation menu ui tree
     * @param $tree array
     * @return string
     */
    public static function menuTree($tree){

        $menuTree = '';

        foreach ($tree as $item) {
            $menuTree .= static::menuItemTpl($item);
        }

        return $menuTree;
    }

}