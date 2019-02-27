<?php

namespace store\models\forms;

use common\models\store\Carts;
use common\models\store\Packages;
use common\models\stores\Stores;
use store\components\validators\LinkValidator;
use store\helpers\UserHelper;
use yii\base\Model;

/**
 * Class AddToCartForm
 * @package app\models\forms
 */
class AddToCartForm extends Model
{
    public $link;

    /**
     * @var Packages
     */
    protected $_package;

    /**
     * @var Stores
     */
    protected $_store;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['link'], 'required'],
            ['link', LinkValidator::class],
        ];
    }

    /**
     * Set package
     * @param Packages $package
     */
    public function setPackage($package)
    {
        $this->_package = $package;
    }

    /**
     * Get package
     * @return Packages
     */
    public function getPackage()
    {
        return $this->_package;
    }

    /**
     * Set store
     * @param Stores $store
     */
    public function setStore($store)
    {
        $this->_store = $store;
    }

    /**
     * Get store
     * @return Stores
     */
    public function getStore()
    {
        return $this->_store;
    }

    /**
     * Save to cart
     * @return bool
     */
    public function save()
    {
        $attributes = $this->attributes;
        if (!$this->validate()) {
            $this->attributes = $attributes;
            return false;
        }

        $cart = new Carts();
        $cart->package_id = $this->_package->id;
        $cart->link = $this->link;
        $cart->generateKey();

        if (!$cart->save()) {
            $this->addError('link', 'Can not add to cart');
            return false;
        }

        UserHelper::addCartKey($cart->key);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'link' => 'Link',
        ];
    }
}