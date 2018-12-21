<?php
namespace app\models\forms;

use app\components\ActiveForm;
use app\helpers\PaymentMethodHelper;
use app\models\main\PaymentMethods;
use payments\Payment;
use app\components\validators\DemoValidator;
use app\helpers\PriceHelper;
use app\models\main\Project;
use app\models\panel\Payments;
use Yii;
use app\models\panel\Users;
use yii\base\DynamicModel;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class AddFoundsForm
 * @package app\models\forms
 */
class AddFoundsForm extends Model {

    /**
     * @var array
     */
    protected static $panelPaymentMethods;

    public $type;
    public $amount;
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
     * @var Users
     */
    protected $_user;

    /**
     * @var Project
     */
    protected $_project;

    /**
     * @var Payments
     */
    protected $_payment;

    /**
     * @var array
     */
    protected $_payments;

    /**
     * @var array
     */
    protected $_paymentsFields;

    /**
     * @var string
     */
    protected $_ip;

    /**
     * @var array
     */
    protected $_userData;

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
            [['amount'], 'safe'],
            [['type'], 'required', 'message' => ''],
            [['type'], 'integer', 'message' => ''],
            [['type'], 'in', 'range' => array_keys($this->getPayments()), 'message' => ''],
            ['fields', 'safe'],
        ];
    }

    /**
     * Set user
     * @param Users $user
     */
    public function setUser($user)
    {
        $this->_user = $user;
    }

    /**
     * Set project
     * @param Project $project
     */
    public function setPanel($project)
    {
        $this->_project = $project;
    }

    /**
     * Set user
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->_ip = $ip;
    }

    /**
     * Get panel model
     * @return Project
     */
    public function getPanel()
    {
        return $this->_project;
    }

    /**
     * @return Payments
     */
    public function getPayment()
    {
        return $this->_payment;
    }

    /**
     * Validate data
     * @param string[]|string $attributeNames
     * @param bool $clearErrors
     * @return bool
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        if (!parent::validate() || !$this->validateAmount('amount') || !$this->validateUserOptions('fields')) {
            return false;
        }

        return true;
    }

    /**
     * Validate fields
     * @param $attribute
     * @return bool
     */
    protected function validateUserOptions($attribute)
    {
        if ($this->hasErrors()) {
            return false;
        }

        $panel = $this->_project;
        $methodOptions = ArrayHelper::getValue($this->getPanelPaymentMethods(), $this->type, []);
        $fields = ArrayHelper::getValue($methodOptions, 'fields', []);
        $paymentMethod = Payment::getPayment($methodOptions['class_name']);
        $paymentMethod->setPanel($panel);
        $paymentMethod->setUser($this->_user);

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
                        if (!$paymentMethod->validate($name, $attributes)) {
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
     * Save function
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $method = ArrayHelper::getValue($this->getPanelPaymentMethods(), $this->_payment->type, []);
        if (empty($method)) {
            return false;
        }

        $payment = Payment::getPayment($method['class_name']);
        $payment->setPanel($this->getPanel());
        $payment->setUser($this->_user);
        $payment->setPaymentMethod($method);
        $result = $payment->checkout($this->_payment);

        return $this->result($method, $result);
    }

    /**
     * Get available payment methods
     * @return array
     */
    protected function getPanelPaymentMethods()
    {
        if (null !== static::$panelPaymentMethods) {
            return static::$panelPaymentMethods;
        }

        static::$panelPaymentMethods = PaymentMethodHelper::getUserAvailablePaymentMethods($this->getPanel(), $this->_user);

        return static::$panelPaymentMethods;
    }

    /**
     * Get payments
     * @return array
     */
    public function getPayments()
    {
        if ($this->_payments) {
            return $this->_payments;
        }

        $this->_payments = [];

        foreach ($this->getPanelPaymentMethods() as $payment) {
            // TODO:: Изменим в новых платежках на элегентное решение
            if (PaymentMethods::METHOD_STRIPE_PAY == $payment['id'] && !$this->_project->ssl) {
                continue;
            }

            $this->_payments[$payment['id']] = [
                'id' => $payment['id'],
                'name' => $payment['name'],
            ];
        }

        return $this->_payments;
    }

    /**
     * Get available payments methods
     * @return array
     */
    public function getPaymentsFields()
    {
        if ($this->_paymentsFields) {
            return $this->_paymentsFields;
        }

        $this->_paymentsFields = [];

        $payments = $this->getPanelPaymentMethods();

        foreach ($payments as $payment) {
            $this->_paymentsFields[$payment['id']] = [];

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

                    $this->_paymentsFields[$payment['id']][$name] = $field;
                }
            }
        }

        // Assign fields post data
        foreach ($this->_paymentsFields as $payment => $fields) {
            foreach ($fields as $key => $field) {
                $name = ArrayHelper::getValue($field, 'name');
                if ($name) {
                    $this->_paymentsFields[$payment][$key]['value'] = ArrayHelper::getValue($this->fields, $name);
                }
            }
        }

        return $this->_paymentsFields;
    }

    /**
     * @return array
     */
    public function getJsOptions()
    {
        $payments = $this->getPanelPaymentMethods();

        foreach ($payments as $payment) {
            $method = ArrayHelper::getValue($this->getPanelPaymentMethods(), $payment['id']);
            $paymentMethod = Payment::getPayment($method['class_name']);
            $paymentMethod->setPanel($this->getPanel());
            $paymentMethod->setUser($this->_user);
            $jsEnvironments = $paymentMethod->getJsEnvironments();

            if (!empty($jsEnvironments['code'])) {
                $this->_jsOptions[$jsEnvironments['code']] = ArrayHelper::merge((array)ArrayHelper::getValue($this->_jsOptions, $payment['id'], []), $jsEnvironments);
            }
        }

        return $this->_jsOptions;
    }

    /**
     * @param array $method
     * @param array $result
     * @return bool
     */
    protected function result($method, $result)
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
}
