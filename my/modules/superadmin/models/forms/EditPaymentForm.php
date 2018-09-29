<?php

namespace my\modules\superadmin\models\forms;

use Yii;
use common\models\panels\PaymentGateway;
use yii\base\Model;
use common\models\panels\Params;

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
     * @var Params
     */
    private $_payment;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['details', 'safe']
        ];
    }

    /**
     * Set super admin
     * @param Params $payment
     */
    public function setPayment(Params $payment)
    {
        $this->_payment = $payment;
        $details = $payment->getOptions();

        $this->name = $details['name'];
        $this->pgid = (int)$details['pgid'];
        $this->visibility = (int)$details['visibility'];
        $this->details = $details;
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

        if (!array_key_exists($this->visibility, PaymentGateway::getVisibilityList())) {
            return false;
        }

        $this->_payment->setOption($this->details);

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
            'name' => Yii::t('app/superadmin', 'payments.edit_modal.method_name'),
            'visibility' => Yii::t('app/superadmin', 'payments.edit_modal.visibility'),
        ];

        switch ($this->pgid) {
            case PaymentGateway::METHOD_TWO_CHECKOUT:
                $labels['account_number'] = Yii::t('app/superadmin', 'payments.2checkout.account_number');
                $labels['secret_word'] = Yii::t('app/superadmin', 'payments.2checkout.secret_word');
                break;

            case PaymentGateway::METHOD_PAYPAL:
                $labels['username'] = Yii::t('app/superadmin', 'payments.paypal.api_username');
                $labels['password'] = Yii::t('app/superadmin', 'payments.paypal.api_password');
                $labels['signature'] = Yii::t('app/superadmin', 'payments.paypal.api_signature');
                break;

            case PaymentGateway::METHOD_PERFECT_MONEY:
                $labels['account'] = Yii::t('app/superadmin', 'payments.perfect_money.usd_account');
                $labels['passphrase'] = Yii::t('app/superadmin', 'payments.perfect_money.passphrase');
                break;

            case PaymentGateway::METHOD_WEBMONEY:
                $labels['purse'] = Yii::t('app/superadmin', 'payments.webmoney.wmz_purse');
                $labels['secret_key'] = Yii::t('app/superadmin', 'payments.webmoney.secret_key');
                break;

            case PaymentGateway::METHOD_BITCOIN:
                $labels['id'] = Yii::t('app/superadmin', 'payments.bitcoin.api_gateway_id');
                $labels['secret'] = Yii::t('app/superadmin', 'payments.bitcoin.gateway_secret');
                break;
        }

        return $labels;
    }
}
