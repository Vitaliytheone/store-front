<?php

namespace my\modules\superadmin\models\forms;

use Yii;
use yii\base\Model;
use common\models\panels\Params;

/**
 * EditPaymentForm is the model behind the Edit Staff form.
 */
class EditPaymentForm extends Model
{
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

        $this->pgid = isset($details['pgid']) ? (int)$details['pgid'] : null;
        $this->visibility = isset($details['visibility']) ? (int)$details['visibility'] : Params::VISIBILITY_DISABLED;
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

        if (!array_key_exists($this->visibility, Params::getVisibilityList())) {
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
            'visibility' => Yii::t('app/superadmin', 'payments.edit_modal.visibility'),
        ];

        switch ($this->pgid) {
            case Params::getPaymentPGID(Params::CODE_TWO_CHECKOUT):
                $labels['account_number'] = Yii::t('app/superadmin', 'payments.2checkout.account_number');
                $labels['secret_word'] = Yii::t('app/superadmin', 'payments.2checkout.secret_word');
                break;

            case Params::getPaymentPGID(Params::CODE_PAYPAL):
                $labels['username'] = Yii::t('app/superadmin', 'payments.paypal.api_username');
                $labels['password'] = Yii::t('app/superadmin', 'payments.paypal.api_password');
                $labels['signature'] = Yii::t('app/superadmin', 'payments.paypal.api_signature');
                break;

            case Params::getPaymentPGID(Params::CODE_PERFECT_MONEY):
                $labels['account'] = Yii::t('app/superadmin', 'payments.perfect_money.usd_account');
                $labels['passphrase'] = Yii::t('app/superadmin', 'payments.perfect_money.passphrase');
                break;

            case Params::getPaymentPGID(Params::CODE_WEBMONEY):
                $labels['purse'] = Yii::t('app/superadmin', 'payments.webmoney.wmz_purse');
                $labels['secret_key'] = Yii::t('app/superadmin', 'payments.webmoney.secret_key');
                break;

            case Params::getPaymentPGID(Params::CODE_BITCOIN):
                $labels['id'] = Yii::t('app/superadmin', 'payments.bitcoin.api_gateway_id');
                $labels['secret'] = Yii::t('app/superadmin', 'payments.bitcoin.gateway_secret');
                break;
        }

        return $labels;
    }
}
