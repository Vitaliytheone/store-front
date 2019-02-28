<?php

namespace control_panel\models\forms;

use control_panel\helpers\UserHelper;
use control_panel\mail\mailers\PasswordChanged;
use common\models\sommerces\Customers;
use common\models\sommerces\MyActivityLog;
use Yii;
use yii\base\Model;

/**
 * Class ChangePasswordForm
 * @package control_panel\models\forms
 */
class ChangePasswordForm extends Model
{
    public $old_password;
    public $password;
    public $password_repeat;

    /**
     * @var Customers
     */
    private $_customer;

    public function __construct(array $config = [])
    {
        $this->_customer = Yii::$app->user->identity;

        return parent::__construct($config);
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['old_password', 'password', 'password_repeat'], 'required'],
            [['password'], 'string', 'max' => 255],
            ['password', 'string', 'min' => 6],
            ['old_password', 'validatePassword'],
            ['password', 'string', 'min' => 6],
            ['password', 'compare'],
        ];
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

        $this->_customer->setPassword($this->password);
        $this->_customer->access_token = hash_hmac('sha256',  $this->_customer->email . '_' . $this->_customer->password . '_' . $_SERVER['REMOTE_ADDR'], Yii::$app->params['access_key']);

        if (!$this->_customer->save()) {
            $this->addErrors($this->_customer->getErrors());
            return false;
        }

        Yii::$app->user->loginByAccessToken($this->_customer->access_token);

        $mail = new PasswordChanged([
            'customer' => $this->_customer
        ]);
        $mail->send();

        MyActivityLog::log(MyActivityLog::E_SETTINGS_UPDATE_PASSWORD, $this->_customer->id, $this->_customer->id, UserHelper::getHash());

        return true;
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (hash_hmac('sha256', $this->old_password, Yii::$app->params['auth_key']) != $this->_customer->password) {
                $this->addError($attribute, Yii::t('app', 'error.customer.incorrect_password'));
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'old_password' => Yii::t('app', 'form.settings_change_password.old_password'),
            'password' => Yii::t('app', 'form.settings_change_password.password'),
            'password_repeat' => Yii::t('app', 'form.settings_change_password.password_repeat'),
        ];
    }
}
