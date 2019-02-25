<?php
namespace superadmin\models\forms;

use common\models\panels\Customers;
use yii\base\Model;
use Yii;

/**
 * EditCustomerForm is the model behind the Edit Customer form.
 */
class EditCustomerForm extends Model
{
    public $email;
    public $referral_status;

    /**
     * @var Customers
     */
    private $_customer;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            [['email', 'referral_status'], 'required'],
            ['referral_status', 'in', 'range' => array_keys($this->getReferrals())],
            [['email'], 'email'],
        ];
    }

    /**
     * Get values and labels of referral status
     * @return array
     */
    public function getReferrals()
    {
        return [
            Customers::REFERRAL_ACTIVE => Yii::t('app/superadmin', 'customers.edit.referral_enabled'),
            Customers::REFERRAL_NOT_ACTIVE => Yii::t('app/superadmin', 'customers.edit.referral_disabled'),
        ];
    }

    /**
     * Set customer
     * @param Customers $customer
     */
    public function setCustomer(Customers $customer)
    {
        $this->_customer = $customer;
    }

    /**
     * Save customer settings
     * @return bool
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->_customer->email = $this->email;
        $this->_customer->referral_status = $this->referral_status;
        $this->_customer->scenario = Customers::SCENARIO_UPDATE;

        if (!$this->_customer->save()) {
            $this->addErrors($this->_customer->getErrors());
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
            'email' => Yii::t('app/superadmin', 'customers.edit.email_label'),
            'referral_status' => Yii::t('app/superadmin', 'customers.edit.referral_status_label'),
        ];
    }
}
