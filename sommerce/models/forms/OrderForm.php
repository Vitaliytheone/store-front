<?php

namespace sommerce\models\forms;

use common\components\ActiveForm;
use common\helpers\CurrencyHelper;
use common\models\store\Checkouts;
use common\models\stores\PaymentMethods;
use common\models\stores\StorePaymentMethods;
use common\models\stores\Stores;
use sommerce\components\payments\Payment;
use sommerce\helpers\UserHelper;
use sommerce\models\search\CartSearch;
use Yii;
use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\base\UnknownClassException;
use yii\helpers\ArrayHelper;

/**
 * Class OrderForm
 * @package app\models\forms
 */
class OrderForm extends Model
{
    /** @var string customer (buyer) email */
    public $email;

    /** @var int current PaymentMethod - ID */
    public $method;

    /** @var int ID of current StorePaymentMethod */
    public $storePayMethod;

    /**
     * @var array
     */
    public $fields;

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
    protected static $_methods;

    /**
     * @var CartSearch
     */
    protected $_searchItems;

    /**
     * @var string
     */
    public $redirect;

    /**
     * @var boolean
     */
    public $refresh = false;

    /**
     * @var array
     */
    public $formData;

    /**
     * @var array
     */
    protected $_currencyPayments;

    /**
     * @var array
     */
    protected $_userData;

    /**
     * @return array the validation rules.
     * @throws UnknownClassException
     */
    public function rules()
    {
        $rules = [];

        $methods = $this->getPaymentMethods();
        $methods = ArrayHelper::index($methods, 'id');

        if (1 == count($methods)) {
            $this->method = key($methods);
        } else {
            $rules[] = [['method'], 'required'];
            $rules[] = [['method'], 'in', 'range' => array_keys($methods)];
        }

        $rules = array_merge($rules, [
            [['email'], 'required'],
            [['email'], 'email'],
            [['email'], 'validateCarItems'],
            [['fields'], 'safe']
        ]);

        return $rules;
    }

    /**
     * Validate data
     * @param null $attributeNames
     * @param bool $clearErrors
     * @return bool
     * @throws InvalidConfigException
     * @throws UnknownClassException
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        if (!parent::validate() || !$this->validateUserOptions('fields')) {
            return false;
        }

        return true;
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
     * @throws UnknownClassException
     */
    public function getPaymentMethods(): array
    {
        if (null === static::$_methods) {

            static::$_methods = [];
            $methods = [];

            foreach (StorePaymentMethods::find()
                 ->store($this->_store)
                 ->active()
                 ->all() as $key => $method) {

                /** @var StorePaymentMethods $method */
                $methods[$key] = [
                    'id' => $method->method_id,
                    'name' => $method->name ?: $method->getName(),
                    'method' => PaymentMethods::getOneMethod($method->method_id),
                    'details' => $method->getOptions(),
                    'position' => $method->position,
                    'fields' => [],
                    'jsOptions' => [],
                    'storePayId' => $method->id,
                ];

                $payMethod = PaymentMethods::findOne($method->method_id);
                $payment = Payment::getPayment($payMethod->method_name);
                $methods[$key]['fields'] = $payment->fields();
                $methods[$key]['jsOptions'] = $payment->getJsEnvironments($this->_store, $this->email, $method);
            }

            ArrayHelper::multisort($methods, 'position', SORT_ASC);

            static::$_methods = ArrayHelper::index($methods, 'method');
        }

        return static::$_methods;
    }

    /**
     * Return Payments methods list for view
     * @return array
     * @throws UnknownClassException
     */
    public function getPaymentsMethodsForView()
    {
        $methods = [];
        foreach ($this->getPaymentMethods() as $method) {
            $methods[] = [
                'id' => $method['id'],
                'method' => $method['name']
            ];
        }

        return $methods;
    }

    /**
     * Save to cart
     * @return bool
     * @throws InvalidConfigException
     * @throws UnknownClassException
     */
    public function save()
    {
        $attributes = $this->attributes;
        if (!$this->validate()) {
            $this->attributes = $attributes;
            return false;
        }

        $payMethod = PaymentMethods::findOne($this->method);
        if (empty($payMethod)) {
            return false;
        }

        // TODO упростить - ищем текущий метод доставя ИД из массива
        $storePayMethodArray = static::$_methods[$payMethod->method_name];

        $storePayMethod = StorePaymentMethods::findOne($storePayMethodArray['storePayId']);
        if (empty($storePayMethod)) {
            return false;
        }


        $checkout = new Checkouts();
        $checkout->customer = $this->email;
        $checkout->method_id = $storePayMethod->id;
        $checkout->price = $this->_searchItems->getTotal();
        $checkout->currency = $this->_store->currency;
        $checkout->currency_id = $storePayMethod->currency_id;
        $checkout->setDetails($this->getItems());
        $checkout->setUserDetails($this->_userData);

        if (!$checkout->save()) {
            $this->addError('email', 'Can not create order.');
            return false;
        }


        $result = Payment::getPayment($payMethod->method_name)->checkout($checkout, $this->_store, $this->email, $storePayMethod);
        if (3 == $result['result'] && !empty($result['refresh'])) {
            $this->refresh = true;
            return true;
        } elseif (2 == $result['result']) {
            $this->redirect = $result['redirect'];
            return true;
        } elseif (1 == $result['result']) {
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

        $this->_currencyPayments = ArrayHelper::index(CurrencyHelper::getPaymentsByCurrency($this->_store->currency), 'code');

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

        return true;
    }

    /**
     * Clear user cart items
     */
    public function clearCart()
    {
        UserHelper::flushCart();
    }

    /**
     * @param $attribute
     * @return bool
     * @throws UnknownClassException
     * @throws InvalidConfigException
     */
    public function validateUserOptions($attribute)
    {
        if ($this->hasErrors()) {
            return false;
        }

        $panel = $this->_store;
        $paymentMethods = ArrayHelper::index($this->getPaymentMethods(), 'id');
        $methodOptions = ArrayHelper::getValue($paymentMethods, $this->method, []);
        $fields = ArrayHelper::getValue($methodOptions, 'fields', []);
        $payMethod = PaymentMethods::findOne($methodOptions['id']);
        $paymentMethod = Payment::getPayment($payMethod->method_name);

        if (empty($fields)) {
            return true;
        }

        $this->_userData = [];
        $rules = [];

        foreach ($fields as $name => $field) {
            $this->_userData[$name] = ArrayHelper::getValue($this->$attribute, $name);
            if (empty($field['rules'])) {
                continue;
            }

            foreach ($field['rules'] as &$rule) {
                if (!empty($rule[1]) && 'method' == $rule[1]) {
                    $attributes = $this->getAttributes();
                    $error = Yii::t('app', ArrayHelper::getValue($rule, 'message'));
                    $rule[1] = function($attribute, $params = []) use ($panel, $paymentMethod, $name, $attributes, $error) {
                        if ($this->hasErrors($attribute)) {
                            return false;
                        }
                        if (!$paymentMethod->validate($panel, $name, $attributes)) {
                            $this->addError($attribute, $error);
                            return false;
                        }

                        return true;
                    };
                }
            }

            $rules = ArrayHelper::merge($rules, $field['rules']);
        }

        if (empty($rules)) {
            return true;
        }

        $model = DynamicModel::validateData($this->_userData, $rules);

        if (!$model->validate()) {
            $error = ActiveForm::firstError($model);
            $this->addError($attribute, Yii::t('app', $error));
            return false;
        }

        return true;
    }

    /**
     * @return array
     * @throws UnknownClassException
     */
    public function getJsOptions()
    {
        $jsOptions = [];

        foreach ($this->getPaymentMethods() as $key => $method) {
            $jsOptions[$method['method']] = $method['jsOptions'];
        }

        return $jsOptions;
    }

    /**
     * Get Fields for available payments methods
     * @return array
     * @throws UnknownClassException
     */
    public function getPaymentsFields()
    {
        $paymentsFields = [];

        foreach ($this->getPaymentMethods() as $key => $payment) {
            $paymentsFields[$payment['id']] = [];

            if (!empty($payment['fields'])) {
                foreach($payment['fields'] as $name => $field) {
                    $field = ArrayHelper::merge($field, [
                        'label' => Yii::t('app', ArrayHelper::getValue($field, 'label')),
                        'type' => $field['type'],
                        'value' => '',
                    ]);

                    if (in_array($field['type'], ['input', 'hidden'])) {
                        $field['name'] = $name;
                    }

                    if (!empty($field['texts'])) {
                        foreach ($field['texts'] as &$text) {
                            $text = Yii::t('app', $text);
                        }
                    } else {
                        $field['texts'] = [];
                    }

                    $paymentsFields[$payment['id']][$name] = $field;
                }
            }
        }

        // Assign fields post data
        foreach ($paymentsFields as $payment => $fields) {
            foreach ($fields as $key => $field) {
                $name = ArrayHelper::getValue($field, 'name');
                if ($name) {
                    $paymentsFields[$payment][$key]['value'] = ArrayHelper::getValue($this->fields, $name);
                }
            }
        }


        return $paymentsFields;
    }
}