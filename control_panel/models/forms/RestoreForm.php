<?php

namespace control_panel\models\forms;

use control_panel\mail\mailers\RestorePassword;
use common\models\sommerces\Customers;
use common\models\sommerces\MyActivityLog;
use Yii;
use yii\base\Model;
use himiklab\yii2\recaptcha\ReCaptchaValidator;

/**
 * Class RestoreForm
 * @package control_panel\models\forms
 */
class RestoreForm extends Model
{
    public $email;

    public $re_captcha;

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
            [['re_captcha'], ReCaptchaValidator::class, 'uncheckedMessage' => Yii::t('app', 'error.restore.incorrect_captcha'), 'message' => Yii::t('app', 'error.restore.incorrect_captcha')],
            [['email'], 'required'],
            ['email', 'customerValidator'],
            ['email', 'email'],
        ];
    }

    /**
     * Restore password
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function restore()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->_customer->generateToken();

        if ($this->_customer->update()) {

            $mail = new RestorePassword([
                'customer' => $this->_customer
            ]);
            $mail->send();

            MyActivityLog::log(MyActivityLog::E_CUSTOMER_FORGOT_PASSWORD, $this->_customer->id, $this->_customer->id, null, $this->_customer->id);

            return true;
        } else {
            $this->addError('email', Yii::t('app', 'error.restore.can_not_restore_password'));
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'form.restore.email'),
        ];
    }

    /**
     * Validator
     * @return bool
     */
    public function customerValidator($attribute)
    {
        if ($this->hasErrors($attribute)) {
            return false;
        }

        $this->_customer = Customers::findOne(['email' => $this->{$attribute}]);

        if (!$this->_customer) {
            $this->addError($attribute, Yii::t('app', 'error.restore.user_not_found'));
            return false;
        }
    }
}