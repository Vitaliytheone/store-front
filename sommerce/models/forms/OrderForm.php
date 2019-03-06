<?php

namespace sommerce\models\forms;

use common\components\ActiveForm;
use common\models\sommerce\Packages;
use sommerce\components\validators\LinkValidator;
use sommerce\helpers\CurrencyHelper;
use common\models\sommerce\Checkouts;
use common\models\sommerces\PaymentMethods;
use common\models\sommerces\StorePaymentMethods;
use common\models\sommerces\Stores;
use sommerce\components\payments\Payment;
use sommerce\helpers\UserHelper;
use sommerce\models\search\CartSearch;
use Yii;
use yii\base\DynamicModel;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\base\UnknownClassException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Class OrderForm
 * @package app\models\forms
 */
class OrderForm extends Model
{
    /**
     * Buyer email
     * @var string customer (buyer) email
     */
    public $email;
    /**
     * @var int current PaymentMethod - ID
     */
    public $method;
    /**
     * Link
     * @var
     */
    public $link;
    /**
     * Package ID
     * @var int $package_id
     */
    public $package_id;
    /**
     * Payment method custom fields
     * @var array
     */
    public $fields;

    /**
     * @var Stores
     */
    protected $_store;
    /**
     * @var  Packages
     */
    protected $_package;

    /**
     * @var array - payment methods
     */
    protected static $_methods;

    /**
     * Result payment method form data
     * @var
     */
    public $formData;

    /**
     * Result payment method redirect
     * @var string
     */
    public $redirect;

    /**
     * Result payment method refresh
     * @var boolean
     */
    public $refresh = false;

    /**
     * @var array
     */
    protected $_currencyPayments;

    /**
     * @var array
     */
    protected $_userData;

    /** @inheritdoc */
    public function formName()
    {
        return 'OrderForm';
    }

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
            ['package_id', 'required'],
            ['package_id', 'exist',
                'targetClass' => Packages::class,
                'targetAttribute' => ['package_id' => 'id'],
                'filter' => ['visibility' => Packages::VISIBILITY_YES]
            ],
            ['link', 'required'],
            ['link', LinkValidator::class],
            ['email', 'required'],
            ['email', 'email'],
            ['fields', 'safe']
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => 'Email address',
            'method' => 'Payment method',
            'package_id' => 'Package',
            'link' => 'Link',
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
     * Get available payment methods
     * @return array
     * @throws UnknownClassException
     */
    public function getPaymentMethods(): array
    {
        if (null === static::$_methods) {

            static::$_methods = [];
            $methods = [];
            $paymentMethods = PaymentMethods::getMethods();
            $names = StorePaymentMethods::getNames();

            foreach (StorePaymentMethods::find()
                 ->store($this->_store)
                 ->active()
                 ->all() as $key => $method) {
                /** @var StorePaymentMethods $method */
                /** @var PaymentMethods $paymentMethod */
                $paymentMethod = ArrayHelper::getValue($paymentMethods, $method->method_id);

                $methods[$key] = [
                    'id' => $method->method_id,
                    'name' => $method->name ?: $names[$method->method_id],
                    'method' => strtolower($paymentMethod->method_name),
                    'details' => $method->getOptions(),
                    'position' => $method->position,
                    'fields' => [],
                    'jsOptions' => [],
                    'storePayId' => $method->id,
                    'class_name' => $paymentMethod->class_name,
                ];

                $payment = Payment::getPayment($paymentMethod->class_name);
                $methods[$key]['fields'] = $payment->fields();
                $methods[$key]['jsOptions'] = $payment->getJsEnvironments($this->_store, $this->email, $method);
            }

            ArrayHelper::multisort($methods, 'position', SORT_ASC);

            static::$_methods = ArrayHelper::index($methods, 'id');
        }

        return static::$_methods;
    }

    /**
     * Return Payments methods list for view
     * @return array
     * @throws UnknownClassException
     */
    public function getPaymentsMethodsForView(): array
    {
        $methods = [];
        foreach ($this->getPaymentMethods() as $method) {

            $methods[] = [
                'id' => $method['id'],
                'name' => Html::encode($method['name']),
                'method' => $method['method'],
            ];
        }

        return $methods;
    }

    /**
     * Proceed to checkout
     * @return bool
     * @throws Exception
     */
    public function save(): bool
    {
        $attributes = $this->attributes;
        if (!$this->validate()) {
            $this->attributes = $attributes;
            return false;
        }

        $storePayMethodArray = static::$_methods[$this->method];

        $storePayMethod = StorePaymentMethods::findOne($storePayMethodArray['storePayId']);
        if (empty($storePayMethod)) {
            return false;
        }

        $checkout = new Checkouts();
        $checkout->customer = $this->email;
        $checkout->method_id = $storePayMethod->method_id;
        $checkout->price = $this->getPackage()->price;
        $checkout->currency = $this->_store->currency;
        $checkout->currency_id = $storePayMethod->currency_id;
        $checkout->redirect_url = Url::previous();
        $checkout->setDetails($this->attributes);
        $checkout->setUserDetails($this->_userData);

        if (!$checkout->save()) {
            throw new Exception('Cannot create checkout!');
        }

        $result = Payment::getPayment($storePayMethodArray['class_name'])->checkout($checkout, $this->_store, $this->email, $storePayMethod);

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
    public function getCurrencyPayments(): array
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
     * @param $attribute
     * @return bool
     * @throws UnknownClassException
     * @throws InvalidConfigException
     */
    public function validateUserOptions($attribute): bool
    {
        if ($this->hasErrors()) {
            return false;
        }

        $panel = $this->_store;
        $paymentMethods = ArrayHelper::index($this->getPaymentMethods(), 'id');
        $methodOptions = ArrayHelper::getValue($paymentMethods, $this->method, []);
        $fields = ArrayHelper::getValue($methodOptions, 'fields', []);
        $paymentMethod = Payment::getPayment($methodOptions['class_name']);

        if (empty($fields)) {
            return true;
        }

        $this->_userData = [];
        $rules = [];

        foreach ($fields as $name => $field) {
            $this->_userData[$name] = ArrayHelper::getValue($this->$attribute, $name, '');
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
    public function getJsOptions(): array
    {
        $jsOptions = [];

        foreach ($this->getPaymentMethods() as $key => $method) {
            $methodName = str_replace(' ', '_', $method['method']);
            $jsOptions[$methodName] = $method['jsOptions'];
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
                    $paymentsFields[$payment][$key]['value'] = ArrayHelper::getValue($this->fields, $name, '');
                }
            }
        }


        return $paymentsFields;
    }
}