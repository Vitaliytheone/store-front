<?php

namespace my\modules\superadmin\models\forms;

use Yii;
use yii\base\Model;
use common\models\panels\Params;

/**
 * EditPaymentForm is the model behind the Edit Payment form.
 */
class EditPaymentForm extends Model
{
    public $code;

    public $name;
    public $visibility;
    public $credentials = [];

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
            ['name', 'string'],
            [['name'], 'string', 'max' => 100],
            [['visibility'], 'integer'],
            ['credentials', 'safe']
        ];
    }

    /**
     * Set super admin
     * @param Params $payment
     */
    public function setPayment(Params $payment)
    {
        $this->_payment = $payment;
        $options = $payment->getOptions();

        $this->code = $payment->code;
        $this->visibility = isset($options['visibility']) ? (int)$options['visibility'] : Params::VISIBILITY_DISABLED;
        $this->attributes = $options;
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

        $this->_payment->setOption($this->attributes);

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
            'name' => Yii::t('app/superadmin', 'payments.edit_modal.name'),
            'visibility' => Yii::t('app/superadmin', 'payments.edit_modal.visibility'),
        ];

        switch ($this->code) {
            case Params::CODE_TWO_CHECKOUT:
                $labels['account_number'] = Yii::t('app/superadmin', 'payments.2checkout.account_number');
                $labels['secret_word'] = Yii::t('app/superadmin', 'payments.2checkout.secret_word');
            break;

            case Params::CODE_PAYPAL:
                $labels['username'] = Yii::t('app/superadmin', 'payments.paypal.api_username');
                $labels['password'] = Yii::t('app/superadmin', 'payments.paypal.api_password');
                $labels['signature'] = Yii::t('app/superadmin', 'payments.paypal.api_signature');
            break;

            case Params::CODE_PERFECT_MONEY:
                $labels['account'] = Yii::t('app/superadmin', 'payments.perfect_money.usd_account');
                $labels['passphrase'] = Yii::t('app/superadmin', 'payments.perfect_money.passphrase');
            break;

            case Params::CODE_WEBMONEY:
                $labels['purse'] = Yii::t('app/superadmin', 'payments.webmoney.wmz_purse');
                $labels['secret_key'] = Yii::t('app/superadmin', 'payments.webmoney.secret_key');
            break;

            case Params::CODE_BITCOIN:
                $labels['id'] = Yii::t('app/superadmin', 'payments.bitcoin.api_gateway_id');
                $labels['secret'] = Yii::t('app/superadmin', 'payments.bitcoin.gateway_secret');
            break;
        }

        return $labels;
    }
}
