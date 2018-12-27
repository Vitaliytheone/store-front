<?php
namespace my\models\forms;

use common\models\panels\Auth;
use common\models\panels\Customers;
use Yii;
use yii\base\Model;

/**
 * Class ResetPasswordForm
 * @package my\models\forms
 */
class ResetPasswordForm extends Model
{
    public $password;
    public $password_repeat;

    /**
     * @var Customers
     */
    public $_customer;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['password', 'password_repeat'], 'required'],
            [['password'], 'compare'],
        ];
    }

    /**
     * Set current customer
     * @param Customers $customer
     */
    public function setCustomer(Customers $customer)
    {
        $this->_customer = $customer;
    }

    /**
     * Reset password
     */
    public function reset()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->_customer->setPassword($this->password);
        $this->_customer->token = null;

        if ($this->_customer->save(false)) {
            $user = Auth::findOne($this->_customer->id);
            $user->setAuthKey();
            $user->save(false);
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password' => Yii::t('app', 'form.reset_password.password'),
            'password_repeat' => Yii::t('app', 'form.reset_password.password_repeat'),
        ];
    }
}