<?php

namespace my\modules\superadmin\models\forms;

use Yii;
use common\models\panels\PaymentGateway;
use yii\base\Model;

/**
 * EditPaymentForm is the model behind the Edit Staff form.
 */
class EditPaymentForm extends Model
{
    public $name;
    public $visibility;
    public $pgid;
    public $details = [];

    /**
     * @var PaymentGateway
     */
    private $_payment;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'visibility'], 'required'],
            [['visibility'], 'in', 'range' => array_keys(PaymentGateway::getVisibilityList())],
            ['details', 'safe']
        ];
    }

    /**
     * Set super admin
     * @param PaymentGateway $payment
     */
    public function setPayment(PaymentGateway $payment)
    {
        $this->_payment = $payment;

        $this->name = $payment->name;
        $this->pgid = $payment->pgid;
        $this->visibility = $payment->visibility;
        $this->details = $payment->getOptionsData();
    }

    /**
     * Save admin settings
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->_payment->name = $this->name;
        $this->_payment->visibility = $this->visibility;
        $this->_payment->setOptionsData($this->details);

        if (!$this->_payment->save()) {
            $this->addErrors($this->_payment->getErrors());
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = [
            'name' => 'Method name',
            'visibility' => 'Visibility'
        ];

        switch ($this->pgid) {
            case PaymentGateway::METHOD_TWO_CHECKOUT:
                $labels['account_number'] = '2Checkout Account Number';
                $labels['secret_word'] = '2Checkout Secret Word';
                break;

            case PaymentGateway::METHOD_PAYPAL:
                $labels['username'] = 'PayPal API Username';
                $labels['password'] = 'PayPal API Password';
                $labels['signature'] = 'PayPal API Signature';
                break;

            case PaymentGateway::METHOD_PERFECT_MONEY:
                $labels['account'] = 'USD Account';
                $labels['passphrase'] = 'Alternate Passphrase';
                break;

            case PaymentGateway::METHOD_WEBMONEY:
                $labels['purse'] = 'WMZ Purse';
                $labels['secret_key'] = 'Secret Key';
                break;

            case PaymentGateway::METHOD_BITCOIN:
                $labels['id'] = 'API Gateway ID';
                $labels['secret'] = 'Gateway secret';
                break;
        }

        return $labels;
    }
}
