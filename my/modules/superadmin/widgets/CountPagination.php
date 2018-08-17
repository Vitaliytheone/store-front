<?php

namespace my\modules\superadmin\widgets;

use yii\base\Widget;
use yii\data\Pagination;
use Yii;

/**
 * Class CountOrders
 * @package app\widgets
 */
class CountPagination extends Widget
{
    /**
     * @var $pages Pagination
     */
    public $pages;
    public $params;

    public static $pageSizeList = [
        100 => 100,
        500 => 500,
        1000 => 1000,
        5000 => 5000,
        'all' => ''
    ];

    /**
     * @var string
     */
    private $content = '';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        if ($this->pages->pageCount > 1 && $this->pages->page + 1 != $this->pages->pageCount) {
            $this->content = strval($this->pages->offset + 1)
                . Yii::t('app/superadmin', 'pages.pagination.to')
                . strval($this->pages->offset + $this->pages->limit)
                . Yii::t('app/superadmin', 'pages.pagination.of')
                .$this->pages->totalCount;
        } else {
            $this->content = strval($this->pages->offset + 1)
                . Yii::t('app/superadmin', 'pages.pagination.to')
                . $this->pages->totalCount
                . Yii::t('app/superadmin', 'pages.pagination.of')
                .$this->pages->totalCount;
        }
        unset($this->params['page_size']);
        static::$pageSizeList['all'] = Yii::t('app/superadmin', 'customers.pagination.all');
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->render('_count_pagination', [
            'content' => $this->content,
            'pages' => $this->pages,
            'params' => $this->params,
        ]);
    }
}
