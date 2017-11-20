<?php
namespace frontend\models\forms;

use common\models\store\Carts;
use common\models\store\Checkouts;
use common\models\stores\PaymentMethods;
use common\models\stores\Stores;
use frontend\helpers\UserHelper;
use frontend\models\search\CartSearch;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

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
     * @var array - payment methods
     */
    protected $_methods;

    /**
     * @var CartSearch
     */
    protected $_searchItems;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        $rules = [];

        $methods = $this->getPaymentMethods();

        if (1 < count($methods)) {
            $rules[] = [['method'], 'required'];
        } else if (1 == count($methods)) {
            $this->method = array_shift($methods);
        }

        $rules = array_merge($rules, [
            [['email'], 'required'],
            [['email'], 'email'],
            [['email'], 'validateCarItems']
        ]);

        return $rules;
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
     * Set cart search
     * @param CartSearch $searchItems
     */
    public function setSearchItems(CartSearch $searchItems)
    {
        $this->_searchItems = $searchItems;

        $this->_items = ArrayHelper::getValue($searchItems->search(), 'models', []);
    }

    /**
     * Get cart items
     * @return array
     */
    public function getItems()
    {
        $returnItems = [];

        foreach ($this->_items as $item) {
            var_dump($item); exit();
        }

        return $returnItems;
    }

    /**
     * Get available payment methods
     * @return array
     */
    public function getPaymentMethods()
    {
        if (null === $this->_methods) {
            $this->_methods = ArrayHelper::map(PaymentMethods::find()->store($this->_store)->active()->all(), 'id', 'name');
        }

        return $this->_methods;
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

        $checkout = new Checkouts();
        $checkout->method_id = $this->method;
        $checkout->price = $this->_searchItems->getTotal();
        $checkout->setDetails($this->getItems());

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

    /**
     * Validate cart items
     * @param $attribute
     * @return bool
     */
    public function validateCarItems($attribute)
    {
        if ($this->hasErrors()) {
            return false;
        }

        if (empty($this->_items)) {
            $this->addError($attribute, 'Cart can not be empty.');
            return false;
        }
    }
}