<?php
namespace gateway\models\forms;

use common\models\gateway\Payments;
use common\models\gateways\PaymentMethods;
use common\models\gateways\SitePaymentMethods;
use common\models\gateways\Sites;
use payments\BasePayment;
use payments\Payment;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class CheckoutForm
 * @package gateway\models\forms
 */
class CheckoutForm extends Model {

    public $method;
    public $source_id;
    public $source_type;
    public $source_payment_id;
    public $method_id;
    public $currency;
    public $amount;
    public $success_url;
    public $fail_url;
    public $return_url;
    public $description;
    public $fields;

    /**
     * @var array
     */
    public $returnData;

    /**
     * @var array
     */
    public $formData;

    /**
     * @var string
     */
    public $redirect;

    /**
     * @var Sites
     */
    protected $_gateway;

    /**
     * @var Payments
     */
    protected $_payment;

    /**
     * @var PaymentMethods
     */
    protected $_method;

    /**
     * @var array
     */
    protected $_userDetails = [];

    /**
     * @var array
     */
    protected $_paymentFields;

    /**
     * @var array
     */
    protected $_jsOptions = [];

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['amount', 'method', 'source_id', 'source_type', 'source_payment_id', 'currency'], 'required'],
            [['description'], 'string'],
            [['description'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'], // Clear for XSS
            [['description'], 'filter', 'filter' => function($value) {
                $value = is_string($value) ? trim($value) : null;
                return !empty($value) ? $value : null;
            }],
            [['amount',], 'number'],
            [['currency',], 'string', 'length' => 3],
            [['success_url', 'fail_url', 'return_url', 'method', 'description'], 'string'],
            [['method_id', 'method_id', 'source_id', 'source_type', 'source_payment_id',], 'integer'],
            ['fields', 'safe'],
        ];
    }

    /**
     * Set gateway
     * @param Sites $gateway
     */
    public function setGateway($gateway)
    {
        $this->_gateway = $gateway;
    }

    /**
     * Get panel model
     * @return Sites
     */
    public function getGateway()
    {
        return $this->_gateway;
    }

    /**
     * @return Payments
     */
    public function getPayment()
    {
        return $this->_payment;
    }

    /**
     * Save function
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        if (!($method = $this->getMethod())) {
            $this->addError('method', '');
            return false;
        }


        $this->method_id = $method->id;
        $this->_payment = new Payments();
        $this->_payment->attributes = $this->attributes;
        $this->_payment->setUserDetails($this->getUserDetails());

        if (!$this->_payment->save(false)) {
            $this->addError('method', '');
            return false;
        }

        $payment = $this->getPaymentMethod();
        $payment->setPayment($this->_payment);
        $payment->setDescription($this->description);
        $result = $payment->checkout();

        return $this->result($result);
    }

    /**
     * @param array $result
     * @return bool
     */
    protected function result($result)
    {
        switch ($result['result']) {
            case 1:
                $this->formData = $result['formData'];
                return true;
            break;

            case 2:
                $this->redirect = $result['redirect'];
                return true;
            break;

            case 3:
                $this->returnData = $result['options'];
                return true;
            break;
        }

        return false;
    }

    /**
     * @return BasePayment
     */
    protected function getPaymentMethod()
    {
        return Payment::getPayment($this->getMethod()->class_name)
            ->setGateway($this->getGateway());
    }

    /**
     * @return PaymentMethods|null
     */
    protected function getMethod()
    {
        if (null === $this->_method) {
            if ($this->hasErrors() || !$this->method) {
                return null;
            }

            $sitePaymentMethod = SitePaymentMethods::find()
                ->innerJoinWith(['method'])
                ->andWhere([
                    'payment_methods.url' => $this->method,
                    'site_id' => $this->getGateway()->id,
                    'visibility' => 1
                ])
                ->one();

            $this->_method = ArrayHelper::getValue($sitePaymentMethod, 'method');
        }

        return $this->_method;
    }

    /**
     * @return bool
     */
    public function validateUserDetails()
    {
        if (!$this->validate()) {
            return false;
        }

        $payment = $this->getPaymentMethod();

        return $payment->validateUserDetails($this->getUserDetails());
    }

    /**
     * @return array
     */
    public function getUserDetails()
    {
        if (null === $this->_userDetails) {
            return $this->_userDetails;
        }
        $this->_userDetails = [];

        $fields = $this->getPaymentMethod()->getFields();
        if (empty($fields)) {
            return $this->_userDetails;
        }

        foreach ((array)$this->fields as $name => $value) {
            if (!empty($fields[$name])) {
                $this->_userDetails[$name] = $value;
            }
        }

        return $this->_userDetails;
    }

    /**
     * @return array
     */
    public function getCheckoutFormData()
    {
        return [
            'form' => [
                'action' => '',
                'method' => 'POST',
                'charset' => 'utf8',
            ],
            'data' => $this->getAttributes([
                'method',
                'source_id',
                'source_type',
                'source_payment_id',
                'method_id',
                'currency',
                'amount',
                'success_url',
                'fail_url',
                'return_url',
                'description',
            ]),
            'auto_redirect' => !$this->referrerDomainValidate(),
        ];
    }

    /**
     * Get available payments methods
     * @return array
     */
    public function getPaymentsFields()
    {
        if ($this->_paymentFields) {
            return $this->_paymentFields;
        }

        $this->_paymentFields = [];


        $paymentMethod = $this->getPaymentMethod();
        $fields = $paymentMethod->getFields();
        $this->_paymentFields = [];

        if (!empty($fields)) {
            foreach ($fields as $name => $field) {
                $field = ArrayHelper::merge($field, [
                    'label' => Yii::t('app', ArrayHelper::getValue($field, 'label')),
                    'type' => $field['type'],
                    'value' => '',
                ]);

                if (in_array($field['type'], ['input', 'hidden', 'checkbox'])) {
                    $field['name'] = $name;
                }

                if (!empty($field['texts'])) {
                    foreach ($field['texts'] as &$text) {
                        $text = Yii::t('app', $text);
                    }
                } else {
                    $field['texts'] = [];
                }

                $this->_paymentFields[$name] = $field;
            }
        }


        // Assign fields post data
        foreach ($this->_paymentFields as $payment => $fields) {
            foreach ($fields as $key => $field) {
                $name = ArrayHelper::getValue($field, 'name');
                if ($name) {
                    $this->_paymentFields[$key]['value'] = ArrayHelper::getValue($this->fields, $name);
                }
            }
        }

        return $this->_paymentFields;
    }

    /**
     * @return array
     */
    public function getJsOptions()
    {
            $paymentMethod = $this->getPaymentMethod();
            $payment = new Payments();
            $payment->attributes = $this->attributes;
            $paymentMethod->setPayment((new Payments($payment)));
            $jsEnvironments = $paymentMethod->getJsEnvironments();

            if (!empty($jsEnvironments['code'])) {
                $this->_jsOptions[$jsEnvironments['code']] = ArrayHelper::merge((array)ArrayHelper::getValue($this->_jsOptions, $jsEnvironments['code'], []), $jsEnvironments);
            }


        return $this->_jsOptions;
    }

    /**
     * @return array
     */
    public function getScripts()
    {
        return $this->getPaymentMethod()->getScripts();
    }

    /**
     * @param $attribute
     * @param array $params
     */
    public function referrerDomainValidate()
    {
        $referrer = parse_url((string)Yii::$app->request->referrer, PHP_URL_HOST);
        $current = parse_url((string)Yii::$app->request->absoluteUrl, PHP_URL_HOST);

        return $referrer == $current;
    }
}
