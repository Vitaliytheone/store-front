<?php
namespace gateway\models\forms;

use common\models\gateway\Payments;
use common\models\gateways\PaymentMethods;
use common\models\gateways\SitePaymentMethods;
use common\models\gateways\Sites;
use payments\Payment;
use Yii;
use yii\base\Model;

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
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['amount', 'method', 'source_id', 'source_type', 'source_payment_id', 'currency'], 'required'],
            [['amount',], 'number'],
            [['currency',], 'string', 'length' => 3],
            [['success_url', 'fail_url', 'return_url', 'method'], 'string'],
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

        if (!($method = $this->getPaymentMethod())) {
            $this->addError('method', '');
        }

        $this->method_id = $method->id;
        $this->_payment = new Payments();
        $this->_payment->attributes = $this->attributes;

        if (!$this->_payment->save(false)) {
            $this->addError('method', '');
            return false;
        }

        $payment = Payment::getPayment($method->class_name);
        $payment->setGateway($this->getGateway());
        $result = $payment->checkout($this->_payment);

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
     * @return PaymentMethods|null
     */
    protected function getPaymentMethod()
    {
        if ($this->hasErrors() || !$this->method) {
            return null;
        }

        return PaymentMethods::find()
            ->innerJoinWith(['sitePaymentMethods'])
            ->andWhere([
                'url' => $this->method,
                'site_payment_methods.visibility' => SitePaymentMethods::VISIBILITY_ENABLED
            ])->one();
    }
}
