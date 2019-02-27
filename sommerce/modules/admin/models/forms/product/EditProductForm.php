<?php

namespace admin\models\forms\product;

use admin\models\forms\BaseForm;
use common\models\store\ActivityLog;
use common\models\store\Products;
use Yii;

/**
 * Class EditProductForm
 * @package admin\models\forms\product
 */
class EditProductForm extends BaseForm
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var Products
     */
    protected $_product;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name',], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @param Products $product
     */
    public function setProduct(Products $product)
    {
        $this->_product = $product;
    }

    /**
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->_product->name = $this->name;
        if (!$this->_product->save(false)) {
            $this->addError('name', Yii::t('admin', 'product.error.can_not_save'));
            return false;
        }

        ActivityLog::log($this->_user, ActivityLog::E_PRODUCTS_PRODUCT_UPDATED, $this->_product->id, $this->_product->id);

        return true;
    }

    public function attributeLabels()
    {
        return [
            'name' => Yii::t('admin', 'products.edit_product.name'),
        ];
    }
}