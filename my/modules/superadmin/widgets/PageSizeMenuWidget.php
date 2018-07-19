<?php
namespace my\modules\superadmin\widgets;

use yii\base\Widget;

/**
 * Class PageSizeMenuWidget
 * @package my\widgets
 */
class PageSizeMenuWidget extends Widget
{
    public $pages;
    public $action;
    public $filters;
    public $countModels;
    public $pageSizes;
    public $pageSize;
    /**
     * @return string
     */
    public function run()
    {
        return $this->render('_page_size_menu', [
            'pages' => $this->pages,
            'action' => $this->action,
            'filters' => $this->filters,
            'countModels' => $this->countModels,
            'pageSizes' => $this->pageSizes,
            'pageSize' => $this->pageSize,
        ]);
    }
}