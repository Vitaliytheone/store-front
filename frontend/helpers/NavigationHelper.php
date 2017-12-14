<?php

namespace frontend\helpers;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

class NavigationsHelper
{

    public static function menuItemTpl($treeItem)
    {
//        if($treeItem['parent_id'] == 0){
//
//            $menuItem = '<li class="dd-item" data-id="2">' . $treeItem['id'] . '</li>';
//
//        }else{
//
//            $menuItem = $treeItem['id'] . '++++' . $treeItem['name'];
//
//        }


        $menuItem =  $treeItem['id'];

        if(isset($treeItem['nodes'])){

            $menuItem .= '<ol>' . static::menuTree($treeItem['nodes']) . '</ol>';

        }

        return '<li>' . $menuItem . '</li>';
    }


    public static function menuTree($tree){

        $menuTree = '';

        foreach($tree as $item){
            $menuTree .= static::menuItemTpl($item);
        }

        return $menuTree;
    }

}