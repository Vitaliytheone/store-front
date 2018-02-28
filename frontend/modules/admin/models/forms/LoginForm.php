<?php
namespace frontend\modules\admin\models\forms;

use common\models\stores\StoreAdminAuth;
use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 * @package frontend\modules\admin\models\forms
 */
class LoginForm extends Model
{
    const COOKIE_LIFETIME = 365 * 24 * 60 * 60; // One year

    public $username;
    public $password;
    private $_user;

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
            [['username', 'password'], 'required'],
            ['password', 'validatePassword'],
            ['username', 'validateStatus'],
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
     * Validate admin status: suspended or active
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function validateStatus($attribute, $params)
    {
        $user = $this->getUser();

        if ($user && !$user->isActive()) {
            $this->addError($attribute, Yii::t('admin', 'login.message_suspended'));
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
        if (!$this->validate()) {
            return false;
        }

        $user = $this->getUser();

        return Yii::$app->user->login($user, static::COOKIE_LIFETIME);
    }

    /**
     * Finds user by Username
     * @return StoreAdminAuth|null
     */
    public function getUser()
    {
        if (!($this->_user instanceof StoreAdminAuth)) {
            $this->_user = StoreAdminAuth::findByUsername($this->username);
        }

        return $this->_user;
    }

}