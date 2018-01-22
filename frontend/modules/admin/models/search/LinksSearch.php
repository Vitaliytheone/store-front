<?php

namespace frontend\modules\admin\models\search;

use common\models\store\Navigation;
use common\models\store\Pages;
use common\models\store\Products;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\db\Query;

class LinksSearch extends Model
{
    private $_storeDb;
    private $_productsTable;
    private $_pagesTable;

    private static $_allowedLinkTypes = [
        Navigation::LINK_PAGE,
        Navigation::LINK_PRODUCT,
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->_storeDb = Yii::$app->store->getInstance()->db_name;
        $this->_productsTable = $this->_storeDb . "." . Products::tableName();
        $this->_pagesTable = $this->_storeDb . "." . Pages::tableName();

        parent::init();
    }

    /**
     * Return array of `pages` links
     * @return array
     */
    public function searchPagesLinks()
    {
        return (new Query())
            ->select(['id', 'title AS name', 'url'])
            ->from($this->_pagesTable)
            ->where([
                'deleted' => Pages::DELETED_NO,
                'visibility' => Pages::VISIBILITY_YES,
            ])
            ->orderBy(['id' => SORT_DESC])
            ->all();
    }

    /**
     * Return array of `products` links
     * @return array
     */
    public function searchProductsLinks()
    {
        return (new Query())
            ->select(['id', 'name', 'url'])
            ->from($this->_productsTable)
            ->where([
                'visibility' => Products::VISIBILITY_YES,
            ])
            ->orderBy(['position' => SORT_ASC])
            ->all();
    }

    /**
     * Return links by link type
     * @param integer $linkType
     * @return array
     * @throws Exception
     */
    public function searchLinksByType(int $linkType)
    {
        if (!in_array($linkType, static::$_allowedLinkTypes)) {
            throw new Exception("Unexpected link type, $linkType");
        }

        $links = [];

        if ($linkType === Navigation::LINK_PAGE) {
            $links =  $this->searchPagesLinks();
        }

        if ($linkType === Navigation::LINK_PRODUCT) {
            $links = $this->searchProductsLinks();
        }

        return $links;
    }
}