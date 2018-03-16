<?php
namespace sommerce\models\forms;

use common\helpers\CurrencyHelper;
use common\models\store\Carts;
use common\models\store\Checkouts;
use common\models\stores\PaymentMethods;
use common\models\stores\Stores;
use sommerce\components\payments\Payment;
use sommerce\helpers\UserHelper;
use sommerce\models\search\CartSearch;
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
     * @var string
     */
    public $redirect;

    /**
     * @var array
     */
    public $formData;

    /**
     * @var array
     */
    protected $_currencyPayments;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        $rules = [];

        $methods = $this->getPaymentMethods();

        if (1 == count($methods)) {
            $this->method = key($methods);
        } else {
            $rules[] = [['method'], 'required'];
            $rules[] = [['method'], 'in', 'range' => array_keys($methods)];
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
            $returnItems[] = [
                'cart_key' => $item['key'],
                'link' => $item['link'],
                'package_id' => $item['package_id'],
                'quantity' => $item['package_quantity'],
            ];
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

            $currencyPayments = $this->getCurrencyPayments();

            $this->_methods = [];
            $methods = [];

            foreach (PaymentMethods::find()
                 ->store($this->_store)
                 ->active()
                 ->all() as $method) {

                if (empty($currencyPayments[$method->method])) {
                    continue;
                }

                $methods[] = [
                    'id' => $method->id,
                    'name' => $method->getName(),
                    'position' => ArrayHelper::getValue($currencyPayments, "$method->method.position", 0),
                ];
            }

            ArrayHelper::multisort($methods, 'position', SORT_ASC);

            foreach ($methods as $method) {
                $this->_methods[$method['id']] = $method['name'];
            }
        }

        return $this->_methods;
    }

    /**
     * Return Payments methods list for view
     * @return array
     */
    public function getPaymentsMethodsForView()
    {
        $methods = $this->_methods;

        if (null === $this->_methods) {
            $methods = $this->getPaymentMethods();
        }

        array_walk($methods, function(&$method, $methodId){
            $method = [
                'id' => $methodId,
                'method' => $method,
            ];
        });

        return $methods;
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
        $checkout->customer = $this->email;
        $checkout->method_id = $this->method;
        $checkout->price = $this->_searchItems->getTotal();
        $checkout->currency = $this->_store->currency;
        $checkout->setDetails($this->getItems());

        if (!$checkout->save()) {
            $this->addError('email', 'Can not create order.');
            return false;
        }

        $paymentMethod = PaymentMethods::find()->andWhere([
            'id' => $this->method
        ])->store($this->_store)->active()->one();


        $result = Payment::getPayment($paymentMethod->method)->checkout($checkout, $this->_store, $this->email, $paymentMethod);

        if (2 == $result['result']) {
            $this->redirect = $result['redirect'];
            return true;
        } else if (1 == $result['result']) {
            $this->formData = $result['formData'];
            return true;
        }

        return false;
    }

    /**
     * Get currency payments
     * @return array
     */
    public function getCurrencyPayments()
    {
        if ($this->_currencyPayments) {
            return $this->_currencyPayments;
        }

        $this->_currencyPayments = CurrencyHelper::getPaymentsByCurrency($this->_store->currency);

        return $this->_currencyPayments;
    }

    /**
     * Get payment config
     * @return mixed
     */
    public function getPaymentConfig()
    {
        return ArrayHelper::getValue($this->getCurrencyPayments(), $this->method, []);
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

    /**
     * Clear user cart items
     */
    public function clearCart()
    {
        UserHelper::flushCart();
    }
}