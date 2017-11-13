<?php
namespace frontend\models\forms;

use common\models\store\Carts;
use common\models\stores\Stores;
use frontend\helpers\UserHelper;
use Yii;
use yii\base\Model;

/**
 * Class OrderForm
 * @package app\models\forms
 */
class OrderForm extends Model {

    public $email;
    public $method;

    /**
     * @var Stores
     */
    protected $_store;

    /**
     * @var array - cart items
     */
    protected $_items;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['email', 'method'], 'required'],
        ];
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
     * Set cart keys
     * @param array $keys
     */
    public function setItems(array $items)
    {
        $this->_items = $items;
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

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => 'Email address',
            'method' => 'Payment method',
        ];
    }
}