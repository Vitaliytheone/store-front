<?php

namespace sommerce\modules\admin\models\forms;

use common\models\store\PageFiles;
use common\models\store\Products;
use Yii;
use common\models\store\Pages;
use common\models\stores\Stores;
use yii\base\Model;
use yii\db\Transaction;

/**
 * Class SaveProductForm
 * @package sommerce\modules\admin\models\forms
 */
class SaveProductForm extends Model
{
    /**
     * @var string
     */
    public $properties;

    /**
     * @var Stores
     */
    protected $_store;

    /**
     * @var Products
     */
    protected $_product;

    /**
     * Set store
     * @param Stores $store
     */
    public function setStore(Stores $store)
    {
        $this->_store = $store;
    }

    /**
     * Return store
     * @return Stores
     */
    public function getStore()
    {
        return $this->_store;
    }

    /**
     * Set page
     * @param Products $product
     */
    public function setProduct(Products $product) {
        $this->_product = $product;
    }

    /**
     * Get product
     * @return Products
     */
    public function getProduct()
    {
        return $this->_product;
    }

    /**
     * @return string
     */
    public function formName()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['properties'], 'required'],
            [['properties'], 'string',],
            [['properties'], 'trim'],
        ];
    }

    /**
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->storeDb->beginTransaction();

        $product = $this->getProduct();

        $product->properties = $this->properties;

        if (!$product->save(false)) {
            $this->addError('page_file', 'Cannot save product!');
            $transaction->rollBack();

            return false;
        }

        $transaction->commit();

        return true;
    }
}