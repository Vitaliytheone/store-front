<?php

namespace sommerce\models\search;

use sommerce\models\forms\ProductViewForm;
use common\models\sommerce\Products;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Class PackagesSearch
 * @package sommerce\models\search
 */
class ProductSearch
{
    private $_product;

    /**
     * PackagesSearch constructor.
     * @param $_product
     */
    public function __construct($_product)
    {
        $this->_product = $_product;
    }

    /**
     * @param ProductViewForm|Products $product
     */
    public function setProduct($product)
    {
        $this->_product = $product;
    }
    
    public  function search()
    {
        return [
            'packages' => array_map(function ($package) {
                return [
                    'id' => $package->id,
                    'best' => $package->best,
                    'quantity' => $package->quantity,
                    'name' => Html::encode($package->name),
                    'price' => $package->price,
                    'button' => [
                        'url_buy_now' => Url::toRoute("/order/$package->id"),
                    ],
                ];
             }, $this->_product->packages),
            'properties' => array_map(function ($property) {
                return Html::encode($property);
            }, $this->_product->properties)
        ];
    }
}