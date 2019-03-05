<?php

namespace sommerce\modules\admin\models\search;

use common\models\sommerce\Navigation;
use common\models\sommerce\Pages;
use common\models\sommerce\Products;
use common\models\sommerces\Stores;
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
     * @param Stores $stores
     */
    public function setStore(Stores $stores)
    {
        $this->_storeDb = $stores->db_name;
        $this->_productsTable = $this->_storeDb . "." . Products::tableName();
        $this->_pagesTable = $this->_storeDb . "." . Pages::tableName();
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
                'visibility' => Pages::VISIBILITY_ON,
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

    /**
     * Return `pages` & `products` urls for blocks
     * @return array
     */
    public function searchLinks4Blocks()
    {
        $links = [
            'pages' => $this->searchPagesLinks(),
            'products' => $this->searchProductsLinks(),
        ];

        foreach ($links as &$linkItem) {
            array_walk($linkItem, function(&$value){
                $value = [
                    'name' => $value['name'],
                    'url' => '/' . $value['url'],
                ];
            });
        }

        return $links;
    }
}