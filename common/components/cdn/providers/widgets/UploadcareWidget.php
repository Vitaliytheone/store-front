<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;


/**
 *
 */
class UploadcareWidget extends Widget
{

    /* @var $searchModel  */
    /* @var $uniqueStatusName  */
    public $searchModel;
    public $uniqueStatusName;


    /**
     *
     * @param $uniqueStatusName
     * @return array
     */
    public static function generateArrayStatusNames(): array
    {

        return $nameList;
    }



    /**
     * @var $statusNameFilter array список статусов в виде массива для фильтра
     * @return string готовое ХТМЛ представление меню
     */
    public function run()
    {

        return $this->render('_status', ['items' => $statusNameFilter,]);
    }

}