<?php

namespace frontend\modules\admin\models\forms;
use common\models\stores\StoreAdmins;
use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property StoreAdmins|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $remember;
    private $_user = false;

    /**
     * @return string
     */
    public function formName()
    {
        return '';
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['remember', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     * @return bool
     */
    public function validatePassword($attribute, $params)
    {
        $user = $this->getUser();

        if (!$user || !$user->validatePassword($this->password)) {
            $this->addError($attribute, Yii::t('admin', 'login.message_bad_login'));

            return false;
        }

        return true;
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->remember ? 3600*24*30 : 0);
        }

        return false;
    }

    /**
     * Finds user by Username
     *
     * @return StoreAdmins|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = StoreAdmins::findByUsername($this->username);
        }

        return $this->_user;
    }

}